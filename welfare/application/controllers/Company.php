<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Company Controller
 *
 * 事業所向けの決済・サブスクリプション管理コントローラー
 *
 * @package    DayCare
 * @subpackage Controllers
 * @category   Payment
 * @author     Claude
 * @version    1.0.0
 */
class Company extends CI_Controller
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();

        // セッションライブラリのロード
        $this->load->library('session');

        // ログインチェック（決済ページは認証必須）
        // staffセッション配列からcompany_idを取得
        $staff = $this->session->userdata('staff');

        // セッションデバッグ（一時的）
        if (empty($staff)) {
            // ログインしていない場合
            redirect('login');
        }

        // company_idの取得（staffまたはログインユーザー情報から）
        $company_id = null;
        if (isset($staff['company_id'])) {
            $company_id = $staff['company_id'];
        } elseif ($this->session->userdata('company_id')) {
            $company_id = $this->session->userdata('company_id');
        }

        if (!$company_id) {
            // company_idが取得できない場合、ログインページへ
            redirect('login');
        }

        // company_idをトップレベルのセッションにも設定（後続処理で使用）
        if (!$this->session->userdata('company_id')) {
            $this->session->set_userdata('company_id', $company_id);
        }

        // Content Security Policyを設定（Stripe対応）
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://code.jquery.com https://cdn.jsdelivr.net https://js.stripe.com chrome-extension:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://www.gstatic.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https: chrome-extension:; connect-src 'self' https://code.jquery.com https://js.stripe.com chrome-extension:; frame-src https://js.stripe.com;");

        // モデルとライブラリのロード
        $this->load->model('Company_model', 'company_model');
        $this->load->model('Payment_model', 'payment_model');
        $this->load->library('Stripe_lib');
        $this->load->helper('url');
    }

    /**
     * 料金プランページ
     *
     * Stripe Pricing Tableを表示
     *
     * @return void
     */
    public function payment()
    {
        $company_id = $this->session->userdata('company_id');

        // 事業所情報を取得
        $data['company'] = $this->company_model->read(['company_id' => $company_id]);

        if (empty($data['company'])) {
            show_404();
            return;
        }

        // Stripe設定を取得
        $stripe_config = $this->config->item('stripe_config');
        $data['stripe_publishable_key'] = $stripe_config['stripe_publishable_key'];
        $data['stripe_pricing_table_id'] = $stripe_config['stripe_pricing_table_id'];

        // サブスクリプション情報を取得
        $data['subscription_status'] = $data['company']['subscription_status'] ?? 'inactive';
        $data['subscription_plan'] = $data['company']['subscription_plan'] ?? null;
        $data['payment_date'] = $data['company']['payment_date'] ?? null;

        // ヘッダー用のユーザー情報を設定
        $data['user'] = [
            'staff_name' => $data['company']['company_name'] ?? '事業所',
            'company_name' => $data['company']['company_name'] ?? '事業所'
        ];
        $data['title'] = '料金プラン';
        $data['page'] = 'payment';
        $data['role'] = defined('ROLE_COMPANY') ? ROLE_COMPANY : 3;

        // ビューを表示
        $this->load->view('company/includes/header', $data);
        $this->load->view('company/payment', $data);
        $this->load->view('company/includes/footer');
    }

    /**
     * Checkout Session作成API
     *
     * Stripeの決済ページへリダイレクトするためのセッションを作成
     *
     * @return void (JSON)
     */
    public function create_checkout_session()
    {
        // JSONリクエストのみ受付
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->output->set_status_header(405);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        // 認証チェック
        $company_id = $this->session->userdata('company_id');
        if (empty($company_id)) {
            $this->output->set_status_header(401);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'error' => '未認証です']);
            return;
        }

        // POSTデータ取得
        $price_id = $this->input->post('price_id');
        $plan_name = $this->input->post('plan_name');

        // バリデーション
        if (empty($price_id)) {
            $this->output->set_status_header(400);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'error' => 'price_id は必須です']);
            return;
        }

        // 事業所情報取得
        $company = $this->company_model->read(['company_id' => $company_id]);

        if (empty($company)) {
            $this->output->set_status_header(404);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'error' => '事業所が見つかりません']);
            return;
        }

        // Checkoutセッション作成
        try {
            $metadata = [];
            if (!empty($plan_name)) {
                $metadata['plan_name'] = $plan_name;
            }

            $session = $this->stripe_lib->createCheckoutSession(
                $price_id,
                $company_id,
                $company['company_email'],
                $metadata
            );

            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => true,
                'session_id' => $session['session_id'],
                'url' => $session['url']
            ]);

        } catch (Exception $e) {
            log_message('error', 'Company: Checkoutセッション作成エラー - ' . $e->getMessage());

            $this->output->set_status_header(500);
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'error' => '決済セッションの作成に失敗しました。しばらくしてから再度お試しください。'
            ]);
        }
    }

    /**
     * 決済成功ページ
     *
     * Stripe Checkoutからのリダイレクト先
     *
     * @return void
     */
    public function payment_success()
    {
        $company_id = $this->session->userdata('company_id');

        // セッションIDを取得（オプション）
        $session_id = $this->input->get('session_id');

        $data['session_id'] = $session_id;
        $data['company'] = $this->company_model->read(['company_id' => $company_id]);

        // ヘッダー用のユーザー情報を設定
        $data['user'] = [
            'staff_name' => $data['company']['company_name'] ?? '事業所'
        ];
        $data['title'] = '決済完了';

        // ビューを表示
        $this->load->view('company/includes/header', $data);
        $this->load->view('company/payment_success', $data);
        $this->load->view('company/includes/footer');
    }

    /**
     * 決済キャンセルページ
     *
     * Stripe Checkoutからのキャンセル時のリダイレクト先
     *
     * @return void
     */
    public function payment_cancel()
    {
        $company_id = $this->session->userdata('company_id');
        $data['company'] = $this->company_model->read(['company_id' => $company_id]);

        // ヘッダー用のユーザー情報を設定
        $data['user'] = [
            'staff_name' => $data['company']['company_name'] ?? '事業所'
        ];
        $data['title'] = '決済キャンセル';

        // ビューを表示
        $this->load->view('company/includes/header', $data);
        $this->load->view('company/payment_cancel', $data);
        $this->load->view('company/includes/footer');
    }

    /**
     * 決済履歴ページ
     *
     * 過去の決済履歴を表示
     *
     * @return void
     */
    public function payment_history()
    {
        $company_id = $this->session->userdata('company_id');

        // ページネーション設定
        $limit = 10;
        $offset = $this->input->get('offset') ?? 0;

        // 決済履歴を取得
        $data['payments'] = $this->payment_model->getPaymentHistory($company_id, $limit, $offset);
        $data['total_count'] = $this->payment_model->getPaymentHistoryCount($company_id);

        // ページネーション情報
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        $data['current_page'] = floor($offset / $limit) + 1;
        $data['total_pages'] = ceil($data['total_count'] / $limit);

        // 事業所情報
        $data['company'] = $this->company_model->read(['company_id' => $company_id]);

        // ヘッダー用のユーザー情報を設定
        $data['user'] = [
            'staff_name' => $data['company']['company_name'] ?? '事業所'
        ];
        $data['title'] = '決済履歴';

        // ビューを表示
        $this->load->view('company/includes/header', $data);
        $this->load->view('company/payment_history', $data);
        $this->load->view('company/includes/footer');
    }

    /**
     * サブスクリプション管理ページ
     *
     * 現在のサブスクリプション情報を表示・管理
     *
     * @return void
     */
    public function subscription()
    {
        $company_id = $this->session->userdata('company_id');
        $data['company'] = $this->company_model->read(['company_id' => $company_id]);

        if (empty($data['company'])) {
            show_404();
            return;
        }

        // サブスクリプション情報を取得
        $subscription_id = $data['company']['stripe_subscription_id'] ?? null;

        if (!empty($subscription_id)) {
            try {
                $data['subscription'] = $this->stripe_lib->getSubscription($subscription_id);
            } catch (Exception $e) {
                log_message('error', 'Company: サブスクリプション取得エラー - ' . $e->getMessage());
                $data['subscription'] = null;
            }
        } else {
            $data['subscription'] = null;
        }

        // 最新の決済情報を取得
        $data['latest_payment'] = $this->payment_model->getLatestPayment($company_id);

        // ビューを表示
        $this->load->view('common/header');
        $this->load->view('company/subscription', $data);
        $this->load->view('common/footer');
    }

    /**
     * サブスクリプションキャンセルAPI
     *
     * サブスクリプションをキャンセル（期間終了時）
     *
     * @return void (JSON)
     */
    public function cancel_subscription()
    {
        // JSONリクエストのみ受付
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->output->set_status_header(405);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        // 認証チェック
        $company_id = $this->session->userdata('company_id');
        if (empty($company_id)) {
            $this->output->set_status_header(401);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'error' => '未認証です']);
            return;
        }

        // 事業所情報取得
        $company = $this->company_model->read(['company_id' => $company_id]);

        if (empty($company) || empty($company['stripe_subscription_id'])) {
            $this->output->set_status_header(404);
            $this->output->set_content_type('application/json');
            echo json_encode(['success' => false, 'error' => 'サブスクリプションが見つかりません']);
            return;
        }

        try {
            // サブスクリプションをキャンセル（期間終了時）
            $subscription = $this->stripe_lib->cancelSubscription(
                $company['stripe_subscription_id'],
                true // 期間終了時にキャンセル
            );

            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => true,
                'message' => 'サブスクリプションのキャンセルを受け付けました。現在の期間終了時にキャンセルされます。'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Company: サブスクリプションキャンセルエラー - ' . $e->getMessage());

            $this->output->set_status_header(500);
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'error' => 'キャンセル処理に失敗しました。しばらくしてから再度お試しください。'
            ]);
        }
    }
}

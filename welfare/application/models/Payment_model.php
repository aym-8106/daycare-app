<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Payment Model
 *
 * Stripe決済履歴管理モデル
 *
 * @package    DayCare
 * @subpackage Models
 * @category   Payment
 * @author     Claude
 * @version    1.0.0
 */
class Payment_model extends CI_Model
{
    /**
     * テーブル名
     * @var string
     */
    protected $table = 'tbl_payment_history';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 決済履歴を記録
     *
     * @param array $data 決済データ
     *   - company_id (int) 必須: 事業所ID
     *   - stripe_customer_id (string) Stripe顧客ID
     *   - stripe_subscription_id (string) StripeサブスクリプションID
     *   - stripe_invoice_id (string) Stripe請求書ID
     *   - stripe_payment_intent_id (string) Stripe PaymentIntent ID
     *   - amount (float) 必須: 決済金額
     *   - currency (string) 通貨コード (デフォルト: jpy)
     *   - status (string) 必須: 決済ステータス (succeeded, failed, pending, refunded)
     *   - plan_name (string) プラン名
     *   - plan_interval (string) 請求間隔 (month, year)
     *   - payment_date (string) 決済日時 (デフォルト: 現在日時)
     *   - next_billing_date (string) 次回請求日
     *   - webhook_event_id (string) StripeイベントID
     *   - failure_reason (string) 失敗理由
     * @return int 挿入されたレコードのID
     */
    public function recordPayment($data)
    {
        // 必須フィールドのバリデーション
        if (empty($data['company_id'])) {
            log_message('error', 'Payment_model: company_id is required');
            return false;
        }

        if (empty($data['amount'])) {
            log_message('error', 'Payment_model: amount is required');
            return false;
        }

        if (empty($data['status'])) {
            log_message('error', 'Payment_model: status is required');
            return false;
        }

        // レコードデータを構築
        $record = [
            'company_id' => $data['company_id'],
            'stripe_customer_id' => $data['stripe_customer_id'] ?? null,
            'stripe_subscription_id' => $data['stripe_subscription_id'] ?? null,
            'stripe_invoice_id' => $data['stripe_invoice_id'] ?? null,
            'stripe_payment_intent_id' => $data['stripe_payment_intent_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'jpy',
            'status' => $data['status'],
            'plan_name' => $data['plan_name'] ?? null,
            'plan_interval' => $data['plan_interval'] ?? null,
            'payment_date' => $data['payment_date'] ?? date('Y-m-d H:i:s'),
            'next_billing_date' => $data['next_billing_date'] ?? null,
            'webhook_event_id' => $data['webhook_event_id'] ?? null,
            'failure_reason' => $data['failure_reason'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // トランザクション開始
        $this->db->trans_start();

        // レコード挿入
        $this->db->insert($this->table, $record);
        $insert_id = $this->db->insert_id();

        // トランザクション完了
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Payment_model: Failed to record payment for company_id=' . $data['company_id']);
            return false;
        }

        log_message('info', 'Payment_model: Payment recorded successfully (id=' . $insert_id . ', company_id=' . $data['company_id'] . ')');

        return $insert_id;
    }

    /**
     * 決済履歴を取得
     *
     * @param int $company_id 事業所ID
     * @param int $limit 取得件数 (デフォルト: 10)
     * @param int $offset オフセット (デフォルト: 0)
     * @return array 決済履歴の配列
     */
    public function getPaymentHistory($company_id, $limit = 10, $offset = 0)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->order_by('payment_date', 'DESC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();

        log_message('info', 'Payment_model: Retrieved ' . $query->num_rows() . ' payment records for company_id=' . $company_id);

        return $query->result_array();
    }

    /**
     * 決済履歴の総件数を取得
     *
     * @param int $company_id 事業所ID
     * @return int 総件数
     */
    public function getPaymentHistoryCount($company_id)
    {
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        return $this->db->count_all_results();
    }

    /**
     * 最新の成功した決済履歴を取得
     *
     * @param int $company_id 事業所ID
     * @return array|null 決済履歴 (見つからない場合はnull)
     */
    public function getLatestPayment($company_id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->where('status', 'succeeded');
        $this->db->order_by('payment_date', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }

    /**
     * 特定のサブスクリプションIDに関連する決済履歴を取得
     *
     * @param string $subscription_id StripeサブスクリプションID
     * @return array 決済履歴の配列
     */
    public function getPaymentsBySubscription($subscription_id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('stripe_subscription_id', $subscription_id);
        $this->db->order_by('payment_date', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 決済ステータスを更新
     *
     * @param int $payment_id 決済履歴ID
     * @param string $status 新しいステータス
     * @param string|null $failure_reason 失敗理由（任意）
     * @return bool 成功/失敗
     */
    public function updatePaymentStatus($payment_id, $status, $failure_reason = null)
    {
        $update_data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($failure_reason !== null) {
            $update_data['failure_reason'] = $failure_reason;
        }

        $this->db->where('id', $payment_id);
        $result = $this->db->update($this->table, $update_data);

        if ($result) {
            log_message('info', 'Payment_model: Payment status updated (id=' . $payment_id . ', status=' . $status . ')');
        } else {
            log_message('error', 'Payment_model: Failed to update payment status (id=' . $payment_id . ')');
        }

        return $result;
    }

    /**
     * 事業所のサブスクリプション情報を更新 (tbl_companyテーブル)
     *
     * @param int $company_id 事業所ID
     * @param array $subscription_data サブスクリプションデータ
     *   - stripe_customer_id (string) Stripe顧客ID
     *   - stripe_subscription_id (string) StripeサブスクリプションID
     *   - subscription_status (string) サブスクリプションステータス
     *   - subscription_plan (string) プラン名
     *   - payment_date (string) 決済日
     *   - subscription_start_date (string) サブスクリプション開始日
     *   - subscription_end_date (string) サブスクリプション終了日
     * @return bool 成功/失敗
     */
    public function updateCompanySubscription($company_id, $subscription_data)
    {
        $update_data = [];

        // 更新するフィールドを構築
        if (isset($subscription_data['stripe_customer_id'])) {
            $update_data['stripe_customer_id'] = $subscription_data['stripe_customer_id'];
        }

        if (isset($subscription_data['stripe_subscription_id'])) {
            $update_data['stripe_subscription_id'] = $subscription_data['stripe_subscription_id'];
        }

        if (isset($subscription_data['subscription_status'])) {
            $update_data['subscription_status'] = $subscription_data['subscription_status'];
        }

        if (isset($subscription_data['subscription_plan'])) {
            $update_data['subscription_plan'] = $subscription_data['subscription_plan'];
        }

        if (isset($subscription_data['payment_date'])) {
            $update_data['payment_date'] = $subscription_data['payment_date'];
        }

        if (isset($subscription_data['subscription_start_date'])) {
            $update_data['subscription_start_date'] = $subscription_data['subscription_start_date'];
        }

        if (isset($subscription_data['subscription_end_date'])) {
            $update_data['subscription_end_date'] = $subscription_data['subscription_end_date'];
        }

        // 更新するデータがない場合は何もしない
        if (empty($update_data)) {
            log_message('warning', 'Payment_model: No data to update for company_id=' . $company_id);
            return false;
        }

        // トランザクション開始
        $this->db->trans_start();

        $this->db->where('company_id', $company_id);
        $this->db->update('tbl_company', $update_data);

        // トランザクション完了
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Payment_model: Failed to update company subscription for company_id=' . $company_id);
            return false;
        }

        log_message('info', 'Payment_model: Company subscription updated successfully (company_id=' . $company_id . ')');

        return true;
    }

    /**
     * 決済履歴をStripeイベントIDで検索
     *
     * @param string $event_id StripeイベントID
     * @return array|null 決済履歴 (見つからない場合はnull)
     */
    public function getPaymentByEventId($event_id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('webhook_event_id', $event_id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }
}

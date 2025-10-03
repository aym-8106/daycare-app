<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Webhook Model
 *
 * Stripe Webhook イベント管理モデル
 * 冪等性を保証するためのイベント処理管理
 *
 * @package    DayCare
 * @subpackage Models
 * @category   Payment
 * @author     Claude
 * @version    1.0.0
 */
class Webhook_model extends CI_Model
{
    /**
     * テーブル名
     * @var string
     */
    protected $table = 'tbl_stripe_webhooks';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * イベントが既に処理済みかチェック
     *
     * @param string $event_id StripeイベントID (evt_xxxxx)
     * @return bool true=処理済み, false=未処理
     */
    public function isEventProcessed($event_id)
    {
        $this->db->where('event_id', $event_id);
        $this->db->where('processed', 1);
        $query = $this->db->get($this->table);

        $is_processed = $query->num_rows() > 0;

        if ($is_processed) {
            log_message('info', 'Webhook_model: Event already processed (event_id=' . $event_id . ')');
        }

        return $is_processed;
    }

    /**
     * イベントが既に記録されているかチェック
     *
     * @param string $event_id StripeイベントID (evt_xxxxx)
     * @return bool true=記録済み, false=未記録
     */
    public function isEventRecorded($event_id)
    {
        $this->db->where('event_id', $event_id);
        $query = $this->db->get($this->table);

        return $query->num_rows() > 0;
    }

    /**
     * イベントを記録
     *
     * @param string $event_id イベントID (evt_xxxxx)
     * @param string $event_type イベントタイプ (例: checkout.session.completed)
     * @param string $payload ペイロード（JSON文字列）
     * @return int 挿入されたレコードのID、または既存レコードのID
     */
    public function recordEvent($event_id, $event_type, $payload)
    {
        // 既に存在する場合は既存のIDを返す
        $this->db->where('event_id', $event_id);
        $existing = $this->db->get($this->table);

        if ($existing->num_rows() > 0) {
            $row = $existing->row();
            log_message('info', 'Webhook_model: Event already recorded (event_id=' . $event_id . ', id=' . $row->id . ')');
            return $row->id;
        }

        // 新規レコードを挿入
        $data = [
            'event_id' => $event_id,
            'event_type' => $event_type,
            'processed' => 0,
            'payload' => $payload,
            'received_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();

        log_message('info', 'Webhook_model: Event recorded (event_id=' . $event_id . ', id=' . $insert_id . ')');

        return $insert_id;
    }

    /**
     * イベントを処理済みとしてマーク
     *
     * @param string $event_id イベントID (evt_xxxxx)
     * @param string $processing_result 処理結果 (デフォルト: 'success')
     * @return bool 成功/失敗
     */
    public function markAsProcessed($event_id, $processing_result = 'success')
    {
        $data = [
            'processed' => 1,
            'processing_result' => $processing_result,
            'processed_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('event_id', $event_id);
        $result = $this->db->update($this->table, $data);

        if ($result) {
            log_message('info', 'Webhook_model: Event marked as processed (event_id=' . $event_id . ', result=' . $processing_result . ')');
        } else {
            log_message('error', 'Webhook_model: Failed to mark event as processed (event_id=' . $event_id . ')');
        }

        return $result;
    }

    /**
     * イベントに処理失敗をマーク
     *
     * @param string $event_id イベントID (evt_xxxxx)
     * @param string $error_message エラーメッセージ
     * @return bool 成功/失敗
     */
    public function markAsFailed($event_id, $error_message)
    {
        $data = [
            'processed' => 0,
            'processing_result' => 'failed: ' . $error_message,
            'processed_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('event_id', $event_id);
        $result = $this->db->update($this->table, $data);

        if ($result) {
            log_message('error', 'Webhook_model: Event marked as failed (event_id=' . $event_id . ', error=' . $error_message . ')');
        } else {
            log_message('error', 'Webhook_model: Failed to mark event as failed (event_id=' . $event_id . ')');
        }

        return $result;
    }

    /**
     * イベント情報を取得
     *
     * @param string $event_id イベントID (evt_xxxxx)
     * @return array|null イベント情報 (見つからない場合はnull)
     */
    public function getEvent($event_id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('event_id', $event_id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }

    /**
     * 未処理のイベントを取得
     *
     * @param int $limit 取得件数 (デフォルト: 10)
     * @return array 未処理イベントの配列
     */
    public function getUnprocessedEvents($limit = 10)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('processed', 0);
        $this->db->order_by('received_at', 'ASC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 特定のイベントタイプの履歴を取得
     *
     * @param string $event_type イベントタイプ (例: invoice.payment_succeeded)
     * @param int $limit 取得件数 (デフォルト: 10)
     * @param int $offset オフセット (デフォルト: 0)
     * @return array イベント履歴の配列
     */
    public function getEventsByType($event_type, $limit = 10, $offset = 0)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('event_type', $event_type);
        $this->db->order_by('received_at', 'DESC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 指定期間のイベントを取得
     *
     * @param string $start_date 開始日 (YYYY-MM-DD HH:MM:SS)
     * @param string $end_date 終了日 (YYYY-MM-DD HH:MM:SS)
     * @return array イベント履歴の配列
     */
    public function getEventsByDateRange($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('received_at >=', $start_date);
        $this->db->where('received_at <=', $end_date);
        $this->db->order_by('received_at', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 処理失敗したイベントを取得
     *
     * @param int $limit 取得件数 (デフォルト: 10)
     * @return array 失敗イベントの配列
     */
    public function getFailedEvents($limit = 10)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('processed', 0);
        $this->db->like('processing_result', 'failed:', 'after');
        $this->db->order_by('received_at', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 古いイベントレコードを削除 (メンテナンス用)
     *
     * @param int $days 指定日数より古いレコードを削除 (デフォルト: 90日)
     * @return int 削除されたレコード数
     */
    public function deleteOldEvents($days = 90)
    {
        $cutoff_date = date('Y-m-d H:i:s', strtotime('-' . $days . ' days'));

        $this->db->where('received_at <', $cutoff_date);
        $this->db->where('processed', 1);
        $this->db->delete($this->table);

        $affected_rows = $this->db->affected_rows();

        log_message('info', 'Webhook_model: Deleted ' . $affected_rows . ' old events (older than ' . $days . ' days)');

        return $affected_rows;
    }

    /**
     * Webhookイベントの統計情報を取得
     *
     * @return array 統計情報
     */
    public function getStatistics()
    {
        $stats = [];

        // 総イベント数
        $stats['total_events'] = $this->db->count_all($this->table);

        // 処理済みイベント数
        $this->db->where('processed', 1);
        $stats['processed_events'] = $this->db->count_all_results($this->table);

        // 未処理イベント数
        $this->db->where('processed', 0);
        $stats['unprocessed_events'] = $this->db->count_all_results($this->table);

        // 失敗イベント数
        $this->db->where('processed', 0);
        $this->db->like('processing_result', 'failed:', 'after');
        $stats['failed_events'] = $this->db->count_all_results($this->table);

        // 最新のイベント日時
        $this->db->select('received_at');
        $this->db->order_by('received_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $stats['latest_event_date'] = $query->row()->received_at;
        } else {
            $stats['latest_event_date'] = null;
        }

        return $stats;
    }
}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class AdminDashboard extends UserController
{
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->header['page'] = 'admin_dashboard';
        $this->header['title'] = '管理者ダッシュボード - CareNavi';
        $this->header['user'] = $this->user;

        $this->load->model('Attendance_model', 'attendance_model');
        $this->load->model('Staff_model', 'staff_model');
        $this->load->model('Company_model', 'company_model');
    }

    /**
     * 管理者ダッシュボードメイン画面
     */
    public function index()
    {
        $company_id = $this->user['company_id'];
        $today = date('Y-m-d');
        $this_month_start = date('Y-m-01');
        $this_month_end = date('Y-m-t');

        // 今日の勤怠状況
        $this->data['today_attendance'] = $this->_get_today_attendance_summary($company_id);

        // 月次統計
        $this->data['monthly_stats'] = $this->attendance_model->get_monthly_statistics(
            $this_month_start,
            $this_month_end,
            $company_id
        );

        // 異常勤怠の検出
        $this->data['anomalies'] = $this->attendance_model->detect_anomalies(
            $this_month_start,
            $this_month_end,
            $company_id
        );

        // 承認待ちの修正申請数
        $this->data['pending_corrections'] = count(
            $this->attendance_model->get_correction_requests(null, 0)
        );

        // 職員の勤怠状況（本日）
        $this->data['staff_status_today'] = $this->_get_staff_status_today($company_id);

        // 遅刻・早退・欠勤の集計（今月）
        $this->data['attendance_summary'] = $this->_get_attendance_summary_this_month($company_id);

        // 残業時間の集計（今月）
        $this->data['overtime_summary'] = $this->_get_overtime_summary_this_month($company_id);

        $this->_load_view('admin_dashboard/index');
    }

    /**
     * リアルタイム勤怠状況
     */
    public function realtime_status()
    {
        $company_id = $this->user['company_id'];
        $today = date('Y-m-d');

        $realtime_data = [
            'working_staff' => $this->_get_currently_working_staff($company_id),
            'late_staff' => $this->_get_late_staff_today($company_id),
            'absent_staff' => $this->_get_absent_staff_today($company_id),
            'overtime_staff' => $this->_get_overtime_staff_today($company_id)
        ];

        echo json_encode($realtime_data);
    }

    /**
     * 週次レポート
     */
    public function weekly_report()
    {
        $company_id = $this->user['company_id'];
        $start_of_week = date('Y-m-d', strtotime('monday this week'));
        $end_of_week = date('Y-m-d', strtotime('sunday this week'));

        $this->data['weekly_stats'] = $this->attendance_model->get_weekly_statistics(
            $start_of_week,
            $end_of_week,
            $company_id
        );

        $this->data['start_date'] = $start_of_week;
        $this->data['end_date'] = $end_of_week;

        $this->_load_view('admin_dashboard/weekly_report');
    }

    /**
     * 職員別勤怠サマリー（AJAX）
     */
    public function staff_summary()
    {
        $staff_id = $this->input->get('staff_id');
        $month = $this->input->get('month') ?: date('Y-m');

        $start_date = $month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $summary = $this->attendance_model->get_monthly_statistics(
            $start_date,
            $end_date,
            null,
            $staff_id
        );

        echo json_encode($summary);
    }

    /**
     * 勤怠アラート設定
     */
    public function alert_settings()
    {
        if ($this->input->post()) {
            $settings = [
                'late_threshold' => $this->input->post('late_threshold'),
                'overtime_threshold' => $this->input->post('overtime_threshold'),
                'continuous_absence_threshold' => $this->input->post('continuous_absence_threshold'),
                'email_notifications' => $this->input->post('email_notifications') ? 1 : 0
            ];

            // 設定をデータベースに保存
            if ($this->_save_alert_settings($settings)) {
                $this->session->set_flashdata('success', 'アラート設定を保存しました。');
            } else {
                $this->session->set_flashdata('error', 'アラート設定の保存に失敗しました。');
            }

            redirect('admin_dashboard/alert_settings');
        }

        $this->data['current_settings'] = $this->_get_alert_settings();
        $this->_load_view('admin_dashboard/alert_settings');
    }

    /**
     * QRコード生成（事業所用打刻QR）
     */
    public function generate_qr_code()
    {
        $company_id = $this->user['company_id'];
        $qr_data = json_encode([
            'company_id' => $company_id,
            'timestamp' => time(),
            'hash' => md5($company_id . date('Y-m-d') . 'carenavi_secret')
        ]);

        $this->load->library('qrcode');
        $qr_code = $this->qrcode->generate($qr_data);

        $this->data['qr_code'] = $qr_code;
        $this->data['qr_data'] = $qr_data;

        $this->_load_view('admin_dashboard/qr_code');
    }

    /**
     * 今日の勤怠状況サマリーを取得
     */
    private function _get_today_attendance_summary($company_id)
    {
        $today = date('Y-m-d');

        $sql = "
            SELECT
                COUNT(CASE WHEN a.work_time IS NOT NULL THEN 1 END) as checked_in,
                COUNT(CASE WHEN a.leave_time IS NOT NULL THEN 1 END) as checked_out,
                COUNT(CASE WHEN a.work_time IS NULL AND s.status = 1 THEN 1 END) as absent,
                COUNT(CASE WHEN TIME(a.work_time) > '09:30:00' THEN 1 END) as late,
                COUNT(CASE WHEN a.overtime_start_time IS NOT NULL THEN 1 END) as overtime
            FROM tbl_staff s
            LEFT JOIN tbl_attendance a ON s.staff_id = a.staff_id AND a.work_date = ?
            WHERE s.company_id = ? AND s.del_flag = 0 AND s.status = 1
        ";

        $query = $this->db->query($sql, [$today, $company_id]);
        return $query->row_array();
    }

    /**
     * 現在勤務中の職員を取得
     */
    private function _get_currently_working_staff($company_id)
    {
        $today = date('Y-m-d');

        $this->db->select('s.staff_name, a.work_time, a.location');
        $this->db->from('tbl_staff s');
        $this->db->join('tbl_attendance a', 's.staff_id = a.staff_id AND a.work_date = "' . $today . '"');
        $this->db->where('s.company_id', $company_id);
        $this->db->where('a.work_time IS NOT NULL');
        $this->db->where('a.leave_time IS NULL');
        $this->db->order_by('a.work_time');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 今日の遅刻者を取得
     */
    private function _get_late_staff_today($company_id)
    {
        $today = date('Y-m-d');

        $this->db->select('s.staff_name, a.work_time');
        $this->db->from('tbl_staff s');
        $this->db->join('tbl_attendance a', 's.staff_id = a.staff_id AND a.work_date = "' . $today . '"');
        $this->db->where('s.company_id', $company_id);
        $this->db->where('TIME(a.work_time) > "09:30:00"'); // 9:30以降を遅刻とする
        $this->db->order_by('a.work_time');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 今日の欠勤者を取得
     */
    private function _get_absent_staff_today($company_id)
    {
        $today = date('Y-m-d');

        $this->db->select('s.staff_name');
        $this->db->from('tbl_staff s');
        $this->db->join('tbl_attendance a', 's.staff_id = a.staff_id AND a.work_date = "' . $today . '"', 'left');
        $this->db->where('s.company_id', $company_id);
        $this->db->where('s.status', 1);
        $this->db->where('s.del_flag', 0);
        $this->db->where('a.work_time IS NULL');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 今日の残業者を取得
     */
    private function _get_overtime_staff_today($company_id)
    {
        $today = date('Y-m-d');

        $this->db->select('s.staff_name, a.overtime_start_time, a.overtime_reason');
        $this->db->from('tbl_staff s');
        $this->db->join('tbl_attendance a', 's.staff_id = a.staff_id AND a.work_date = "' . $today . '"');
        $this->db->where('s.company_id', $company_id);
        $this->db->where('a.overtime_start_time IS NOT NULL');
        $this->db->order_by('a.overtime_start_time');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 職員の今日の勤怠状況を取得
     */
    private function _get_staff_status_today($company_id)
    {
        $today = date('Y-m-d');

        $this->db->select('
            s.staff_id,
            s.staff_name,
            a.work_time,
            a.leave_time,
            a.location,
            CASE
                WHEN a.work_time IS NULL THEN "absent"
                WHEN a.leave_time IS NULL THEN "working"
                ELSE "finished"
            END as status
        ');
        $this->db->from('tbl_staff s');
        $this->db->join('tbl_attendance a', 's.staff_id = a.staff_id AND a.work_date = "' . $today . '"', 'left');
        $this->db->where('s.company_id', $company_id);
        $this->db->where('s.del_flag', 0);
        $this->db->where('s.status', 1);
        $this->db->order_by('s.staff_name');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 今月の遅刻・早退・欠勤集計を取得
     */
    private function _get_attendance_summary_this_month($company_id)
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        $sql = "
            SELECT
                COUNT(CASE WHEN TIME(a.work_time) > '09:30:00' THEN 1 END) as late_count,
                COUNT(CASE WHEN TIME(a.leave_time) < '17:30:00' AND a.leave_time IS NOT NULL THEN 1 END) as early_leave_count,
                COUNT(CASE WHEN a.work_time IS NULL AND s.status = 1 THEN 1 END) as absent_count
            FROM tbl_staff s
            LEFT JOIN tbl_attendance a ON s.staff_id = a.staff_id
                AND a.work_date BETWEEN ? AND ?
            WHERE s.company_id = ? AND s.del_flag = 0
        ";

        $query = $this->db->query($sql, [$start_date, $end_date, $company_id]);
        return $query->row_array();
    }

    /**
     * 今月の残業時間集計を取得
     */
    private function _get_overtime_summary_this_month($company_id)
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        $sql = "
            SELECT
                SUM(TIME_TO_SEC(TIMEDIFF(a.overtime_end_time, a.overtime_start_time))) / 3600 as total_overtime_hours,
                COUNT(CASE WHEN a.overtime_start_time IS NOT NULL THEN 1 END) as overtime_days,
                AVG(TIME_TO_SEC(TIMEDIFF(a.overtime_end_time, a.overtime_start_time))) / 3600 as avg_overtime_hours
            FROM tbl_staff s
            JOIN tbl_attendance a ON s.staff_id = a.staff_id
            WHERE s.company_id = ? AND s.del_flag = 0
                AND a.work_date BETWEEN ? AND ?
                AND a.overtime_start_time IS NOT NULL
        ";

        $query = $this->db->query($sql, [$company_id, $start_date, $end_date]);
        return $query->row_array();
    }

    /**
     * アラート設定を保存
     */
    private function _save_alert_settings($settings)
    {
        $company_id = $this->user['company_id'];

        // 既存設定があるかチェック
        $existing = $this->db->get_where('tbl_alert_settings', ['company_id' => $company_id])->row();

        $data = array_merge($settings, [
            'company_id' => $company_id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($existing) {
            $this->db->where('company_id', $company_id);
            return $this->db->update('tbl_alert_settings', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('tbl_alert_settings', $data);
        }
    }

    /**
     * 現在のアラート設定を取得
     */
    private function _get_alert_settings()
    {
        $company_id = $this->user['company_id'];
        $settings = $this->db->get_where('tbl_alert_settings', ['company_id' => $company_id])->row_array();

        // デフォルト設定
        if (!$settings) {
            return [
                'late_threshold' => 30,
                'overtime_threshold' => 60,
                'continuous_absence_threshold' => 3,
                'email_notifications' => 1
            ];
        }

        return $settings;
    }
}
?>
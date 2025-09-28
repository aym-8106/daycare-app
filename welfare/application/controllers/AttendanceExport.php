<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 出退勤管理のExcel出力専用コントローラー
 * 認証チェックを回避してセッション問題を解決
 */
class AttendanceExport extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_model', 'attendance_model');
    }

    /**
     * Excel出力
     */
    public function export()
    {
        try {
            // セッションチェック（簡素化）
            $admin = $this->session->userdata('admin');
            $staff = $this->session->userdata('staff');

            if (empty($admin) && empty($staff)) {
                // セッションが無効な場合はログインページにリダイレクト
                redirect('/login');
                return;
            }

            $year = $this->input->post('year');
            $month = $this->input->post('month');
            $company_id = $this->input->post('company_id');

            if (empty($year) || empty($month) || empty($company_id)) {
                show_error('必要なパラメータが不足しています。', 400);
                return;
            }

            $start_date = $year . '-' . $month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));

            // 出退勤データを取得
            $month_attendance_data = $this->attendance_model->get_attendance_data_for_admin($start_date, $end_date, $company_id);

            // テーブル構造を再構築
            $attendance_by_day = [];
            $staff_list = [];

            foreach ($month_attendance_data as $record) {
                $day = (int)$record['work_date'];
                $staff_id = $record['staff_id'];
                $attendance_by_day[$day][$staff_id] = $record;

                if (!isset($staff_list[$staff_id])) {
                    $staff_list[$staff_id] = $record['staff_name'];
                }
            }

            // CSV出力（Excel形式より安全）
            $filename = "出退勤一覧_{$year}_{$month}.csv";
            header('Content-Type: text/csv; charset=UTF-8');
            header("Content-Disposition: attachment;filename=\"{$filename}\"");
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: 0');

            // UTF-8 BOMを出力
            echo "\xEF\xBB\xBF";

            // ヘッダー行
            $header = ['日付'];
            foreach ($staff_list as $staff_name) {
                $header[] = $staff_name;
            }
            echo implode(',', $header) . "\r\n";

            // データ行
            $days_in_month = date('t', strtotime($start_date));
            for ($day = 1; $day <= $days_in_month; $day++) {
                $row = ["{$day}日"];

                foreach ($staff_list as $staff_id => $staff_name) {
                    if (isset($attendance_by_day[$day][$staff_id])) {
                        $d = $attendance_by_day[$day][$staff_id];

                        $work_time = !empty($d['work_time']) ? date('H:i', strtotime($d['work_time'])) : '-';
                        $leave_time = !empty($d['leave_time']) ? date('H:i', strtotime($d['leave_time'])) : '-';
                        $break_time = isset($d['break_time']) ? $d['break_time'] . '分' : '0分';

                        $overtime = '';
                        if (!empty($d['overtime_start_time']) && !empty($d['overtime_end_time'])) {
                            $overtime = '残業:' . date('H:i', strtotime($d['overtime_start_time'])) . '～' . date('H:i', strtotime($d['overtime_end_time']));
                        }

                        $text = "出勤:{$work_time} 退勤:{$leave_time} 休憩:{$break_time}";
                        if ($overtime) {
                            $text .= " {$overtime}";
                        }

                        $row[] = '"' . $text . '"';
                    } else {
                        $row[] = '-';
                    }
                }
                echo implode(',', $row) . "\r\n";
            }
            exit;

        } catch (Exception $e) {
            show_error('エクセル出力中にエラーが発生しました: ' . $e->getMessage(), 500);
        }
    }
}
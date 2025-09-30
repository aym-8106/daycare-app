<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

/**
 * 出退勤管理のExcel出力専用コントローラー
 */
class AttendanceExport extends AdminController
{
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);
        $this->load->model('Attendance_model', 'attendance_model');
    }

    /**
     * Excel出力
     */
    public function export()
    {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month');
            $company_id = $this->input->post('company_id');
            $selected_staff = $this->input->post('selected_staff'); // スタッフIDの配列

            if (empty($year) || empty($month) || empty($company_id)) {
                show_error('必要なパラメータが不足しています。', 400);
                return;
            }

            $start_date = $year . '-' . $month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));

            // 出退勤データを取得
            $month_attendance_data = $this->attendance_model->get_attendance_data_for_admin($start_date, $end_date, $company_id);

            // 同じ事業所の全スタッフを取得
            $all_staff = $this->attendance_model->get_company_staff($company_id);

            // スタッフリストを構築（選択されたスタッフのみ、または全員）
            $staff_list = [];
            if (!empty($selected_staff) && is_array($selected_staff)) {
                foreach ($all_staff as $staff) {
                    if (in_array($staff['staff_id'], $selected_staff)) {
                        $staff_list[$staff['staff_id']] = $staff['staff_name'];
                    }
                }
            } else {
                // 全スタッフを含める
                foreach ($all_staff as $staff) {
                    $staff_list[$staff['staff_id']] = $staff['staff_name'];
                }
            }

            // 出退勤データをスタッフIDと日付でインデックス化
            $attendance_by_day = [];
            foreach ($month_attendance_data as $record) {
                $day = (int)$record['work_date'];
                $staff_id = $record['staff_id'];
                $attendance_by_day[$day][$staff_id] = $record;
            }

            // CSV出力
            $filename = "出退勤一覧_{$year}年{$month}月.csv";
            header('Content-Type: text/csv; charset=UTF-8');
            header("Content-Disposition: attachment;filename=\"{$filename}\"");
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: 0');

            // UTF-8 BOMを出力（Excelで正しく表示するため）
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

                        $work_time = !empty($d['work_time']) && $d['work_time'] != '00:00' ? $d['work_time'] : '-';
                        $leave_time = !empty($d['leave_time']) && $d['leave_time'] != '00:00' ? $d['leave_time'] : '-';

                        $break_time = '-';
                        if (isset($d['total_break_time']) && $d['total_break_time'] > 0) {
                            if ($d['total_break_time'] < 60) {
                                $break_time = '1分未満';
                            } else {
                                $break_time = floor($d['total_break_time'] / 60) . '分';
                            }
                        }

                        $overtime = '';
                        if (!empty($d['overtime_start_time']) && !empty($d['overtime_end_time'])
                            && $d['overtime_start_time'] != '00:00' && $d['overtime_end_time'] != '00:00') {
                            $overtime = ' 残業:' . $d['overtime_start_time'] . '～' . $d['overtime_end_time'];
                        }

                        $text = "出勤:{$work_time} 退勤:{$leave_time} 休憩:{$break_time}{$overtime}";
                        $row[] = '"' . $text . '"';
                    } else {
                        $row[] = '-';
                    }
                }
                echo implode(',', $row) . "\r\n";
            }
            exit;

        } catch (Exception $e) {
            show_error('Excel出力中にエラーが発生しました: ' . $e->getMessage(), 500);
        }
    }
}
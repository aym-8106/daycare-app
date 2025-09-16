<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class AttendanceReport extends UserController
{
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->header['page'] = 'attendance_report';
        $this->header['title'] = '勤怠レポート - CareNavi';
        $this->header['user'] = $this->user;

        $this->load->model('Attendance_model', 'attendance_model');
        $this->load->model('Staff_model', 'staff_model');
        $this->load->model('Company_model', 'company_model');
        $this->load->library('excel');
    }

    /**
     * 勤怠データ一覧・検索画面
     */
    public function index()
    {
        $search_params = [
            'start_date' => $this->input->get('start_date') ?: date('Y-m-01'),
            'end_date' => $this->input->get('end_date') ?: date('Y-m-t'),
            'staff_id' => $this->input->get('staff_id'),
            'department' => $this->input->get('department'),
            'status' => $this->input->get('status')
        ];

        $page = $this->input->get('page', TRUE) ?: 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        // 管理者の場合は自社のデータのみ
        $company_id = $this->user['company_id'];

        // 勤怠データの取得
        $attendance_data = $this->attendance_model->get_attendance_data_for_report(
            $search_params['start_date'],
            $search_params['end_date'],
            $company_id,
            $search_params['staff_id'],
            $limit,
            $offset
        );

        $this->data['attendance_list'] = $attendance_data;
        $this->data['search_params'] = $search_params;

        // 職員一覧（検索用）
        $this->data['staff_list'] = $this->staff_model->getList(
            'staff_id, staff_name',
            ['BaseTbl.company_id' => $company_id, 'BaseTbl.del_flag' => 0]
        );

        // ページネーション情報
        $total_count = $this->attendance_model->count_attendance_data_for_report(
            $search_params['start_date'],
            $search_params['end_date'],
            $company_id,
            $search_params['staff_id']
        );

        $this->data['pagination'] = [
            'current_page' => $page,
            'total_pages' => ceil($total_count / $limit),
            'total_count' => $total_count
        ];

        $this->_load_view('attendance_report/index');
    }

    /**
     * 月次勤怠レポート
     */
    public function monthly()
    {
        $year = $this->input->get('year') ?: date('Y');
        $month = $this->input->get('month') ?: date('m');
        $staff_id = $this->input->get('staff_id');

        $start_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $company_id = $this->user['company_id'];

        // 月次統計データの取得
        $monthly_stats = $this->attendance_model->get_monthly_statistics(
            $start_date,
            $end_date,
            $company_id,
            $staff_id
        );

        $this->data['monthly_stats'] = $monthly_stats;
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['selected_staff_id'] = $staff_id;

        // 職員一覧
        $this->data['staff_list'] = $this->staff_model->getList(
            'staff_id, staff_name',
            ['BaseTbl.company_id' => $company_id, 'BaseTbl.del_flag' => 0]
        );

        // 異常勤怠の検出
        $this->data['anomalies'] = $this->attendance_model->detect_anomalies(
            $start_date,
            $end_date,
            $company_id
        );

        $this->_load_view('attendance_report/monthly');
    }

    /**
     * 勤怠詳細レポート（個人別）
     */
    public function detail($staff_id)
    {
        if (!$staff_id) {
            redirect('attendance_report');
        }

        $year = $this->input->get('year') ?: date('Y');
        $month = $this->input->get('month') ?: date('m');

        $start_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        // 権限チェック：管理者は自社の職員のみ
        $staff = $this->staff_model->getById($staff_id);
        if (!$staff || $staff['company_id'] != $this->user['company_id']) {
            show_error('権限がありません。', 403);
        }

        // 詳細勤怠データの取得
        $attendance_details = $this->attendance_model->get_attendance_data(
            $start_date,
            $end_date,
            $staff_id
        );

        // 勤務予定との比較
        $schedule_comparison = $this->attendance_model->get_schedule_vs_actual(
            $start_date,
            $end_date,
            $staff_id
        );

        $this->data['staff'] = $staff;
        $this->data['attendance_details'] = $attendance_details;
        $this->data['schedule_comparison'] = $schedule_comparison;
        $this->data['year'] = $year;
        $this->data['month'] = $month;

        $this->_load_view('attendance_report/detail');
    }

    /**
     * CSVエクスポート
     */
    public function export_csv()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $staff_id = $this->input->post('staff_id');
        $company_id = $this->user['company_id'];

        $attendance_data = $this->attendance_model->get_attendance_data_for_export(
            $start_date,
            $end_date,
            $company_id,
            $staff_id
        );

        $filename = '勤怠データ_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=shift_jis');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // ヘッダー行
        $headers = [
            '職員名', '勤務日', '出勤時刻', '退勤時刻', '休憩時間（分）',
            '実働時間', '残業開始', '残業終了', '残業時間', '勤務場所', 'メモ'
        ];
        fputcsv($output, $headers);

        // データ行
        foreach ($attendance_data as $record) {
            $row = [
                $record['staff_name'],
                $record['work_date'],
                $record['work_time'],
                $record['leave_time'],
                $record['total_break_time'],
                $this->_calculate_work_hours($record['work_time'], $record['leave_time'], $record['total_break_time']),
                $record['overtime_start_time'],
                $record['overtime_end_time'],
                $record['overtime_duration'],
                $record['location'],
                $record['memo']
            ];

            // Shift_JISに変換
            $row = array_map(function($item) {
                return mb_convert_encoding($item, 'SJIS', 'UTF-8');
            }, $row);

            fputcsv($output, $row);
        }

        fclose($output);
    }

    /**
     * Excelエクスポート
     */
    public function export_excel()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $staff_id = $this->input->post('staff_id');
        $company_id = $this->user['company_id'];

        $attendance_data = $this->attendance_model->get_attendance_data_for_export(
            $start_date,
            $end_date,
            $company_id,
            $staff_id
        );

        // PHPExcelを使ったExcel生成
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet = $objPHPExcel->getActiveSheet();

        // ヘッダー設定
        $activeSheet->setCellValue('A1', '勤怠データレポート')
                   ->setCellValue('A2', '期間: ' . $start_date . ' ～ ' . $end_date)
                   ->setCellValue('A4', '職員名')
                   ->setCellValue('B4', '勤務日')
                   ->setCellValue('C4', '出勤時刻')
                   ->setCellValue('D4', '退勤時刻')
                   ->setCellValue('E4', '休憩時間（分）')
                   ->setCellValue('F4', '実働時間')
                   ->setCellValue('G4', '残業開始')
                   ->setCellValue('H4', '残業終了')
                   ->setCellValue('I4', '残業時間')
                   ->setCellValue('J4', '勤務場所')
                   ->setCellValue('K4', 'メモ');

        // データ行の追加
        $row = 5;
        foreach ($attendance_data as $record) {
            $activeSheet->setCellValue('A' . $row, $record['staff_name'])
                       ->setCellValue('B' . $row, $record['work_date'])
                       ->setCellValue('C' . $row, $record['work_time'])
                       ->setCellValue('D' . $row, $record['leave_time'])
                       ->setCellValue('E' . $row, $record['total_break_time'])
                       ->setCellValue('F' . $row, $this->_calculate_work_hours($record['work_time'], $record['leave_time'], $record['total_break_time']))
                       ->setCellValue('G' . $row, $record['overtime_start_time'])
                       ->setCellValue('H' . $row, $record['overtime_end_time'])
                       ->setCellValue('I' . $row, $record['overtime_duration'])
                       ->setCellValue('J' . $row, $record['location'])
                       ->setCellValue('K' . $row, $record['memo']);
            $row++;
        }

        // スタイル設定
        $activeSheet->getStyle('A1:K4')->getFont()->setBold(true);
        $activeSheet->getStyle('A4:K4')->getFill()
                   ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                   ->getStartColor()->setRGB('CCCCCC');

        // 列幅の自動調整
        foreach (range('A', 'K') as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = '勤怠データ_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 勤怠修正申請一覧
     */
    public function correction_requests()
    {
        $status = $this->input->get('status');
        $company_id = $this->user['company_id'];

        $correction_requests = $this->attendance_model->get_correction_requests_for_company(
            $company_id,
            $status
        );

        $this->data['correction_requests'] = $correction_requests;
        $this->data['selected_status'] = $status;

        $this->_load_view('attendance_report/correction_requests');
    }

    /**
     * 勤怠修正申請の承認・却下処理
     */
    public function approve_correction()
    {
        $correction_id = $this->input->post('correction_id');
        $action = $this->input->post('action'); // 'approve' or 'reject'
        $rejection_reason = $this->input->post('rejection_reason');

        $status = ($action === 'approve') ? 1 : 2;

        if ($this->attendance_model->approve_correction(
            $correction_id,
            $status,
            $this->user['staff_id'],
            $rejection_reason
        )) {
            // 承認の場合は実際の勤怠データも更新
            if ($action === 'approve') {
                $this->_apply_correction($correction_id);
            }

            echo json_encode([
                'success' => true,
                'message' => $action === 'approve' ? '申請を承認しました。' : '申請を却下しました。'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '処理に失敗しました。'
            ]);
        }
    }

    /**
     * 承認された修正を実際の勤怠データに適用
     */
    private function _apply_correction($correction_id)
    {
        $correction = $this->attendance_model->get_correction_by_id($correction_id);

        if ($correction && $correction['status'] == 1) {
            $update_data = [];

            switch ($correction['correction_type']) {
                case 1: // 出勤時刻
                    $update_data['work_time'] = $correction['corrected_value'];
                    break;
                case 2: // 退勤時刻
                    $update_data['leave_time'] = $correction['corrected_value'];
                    break;
                case 3: // 休憩時間
                    $update_data['break_time'] = $correction['corrected_value'];
                    break;
            }

            if (!empty($update_data)) {
                $this->attendance_model->update_attendance_record(
                    $correction['attendance_id'],
                    $update_data
                );
            }
        }
    }

    /**
     * 実働時間の計算
     */
    private function _calculate_work_hours($start_time, $end_time, $break_minutes)
    {
        if (!$start_time || !$end_time) {
            return '00:00';
        }

        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $work_seconds = $end - $start - ($break_minutes * 60);

        $hours = floor($work_seconds / 3600);
        $minutes = floor(($work_seconds % 3600) / 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
?>
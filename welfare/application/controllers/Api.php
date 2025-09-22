<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/ApiController.php';

/*
 *  Api
 */

class Api extends ApiController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('company_model');

        if ($this->input->server('REQUEST_METHOD') === 'OPTIONS') {
            echo(json_encode(array('result' => 'fail')));
            exit;
        }
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        echo(json_encode([]));
    }

    /**
     * 出退勤データ取得API
     */
    public function get_attendance_data()
    {
        header('Content-Type: application/json');

        try {
            // セッションチェック
            $admin = $this->session->userdata('admin');
            $staff = $this->session->userdata('staff');

            if (empty($admin) && empty($staff)) {
                echo json_encode(['success' => false, 'message' => 'セッションが無効です。']);
                return;
            }

            $attendance_id = $this->input->post('attendance_id');

            if (!$attendance_id) {
                echo json_encode(['success' => false, 'message' => 'attendance_idが指定されていません。']);
                return;
            }

            // 出退勤データを取得
            $this->db->where('attendance_id', $attendance_id);
            $attendance_data = $this->db->get('tbl_attendance')->row_array();

            if ($attendance_data) {
                echo json_encode(['success' => true, 'data' => $attendance_data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'データが見つかりませんでした。']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'エラー: ' . $e->getMessage()]);
        }
    }

    /**
     * 出退勤データ更新API
     */
    public function update_attendance()
    {
        header('Content-Type: application/json');

        try {
            // セッションチェック
            $admin = $this->session->userdata('admin');
            $staff = $this->session->userdata('staff');

            if (empty($admin) && empty($staff)) {
                echo json_encode(['success' => false, 'message' => 'セッションが無効です。']);
                return;
            }

            $attendance_id = $this->input->post('attendance_id');
            $work_time = $this->input->post('work_time');
            $leave_time = $this->input->post('leave_time');
            $break_time = $this->input->post('break_time');
            $overtime_start_time = $this->input->post('overtime_start_time');
            $overtime_end_time = $this->input->post('overtime_end_time');

            if (!$attendance_id) {
                echo json_encode(['success' => false, 'message' => 'attendance_idが指定されていません。']);
                return;
            }

            // 現在の出退勤データを取得して日付部分を保持
            $this->db->where('attendance_id', $attendance_id);
            $current_data = $this->db->get('tbl_attendance')->row_array();

            if (!$current_data) {
                echo json_encode(['success' => false, 'message' => '出退勤データが見つかりませんでした。']);
                return;
            }

            $work_date = $current_data['work_date'];

            // 編集するデータを準備（datetime形式で保存）
            $update_data = array();
            if (!empty($work_time)) {
                $update_data['work_time'] = $work_date . ' ' . $work_time . ':00';
            }
            if (!empty($leave_time)) {
                $update_data['leave_time'] = $work_date . ' ' . $leave_time . ':00';
            }
            if (isset($break_time) && $break_time !== '') {
                $update_data['break_time'] = $break_time;
            }
            if (!empty($overtime_start_time)) {
                $update_data['overtime_start_time'] = $work_date . ' ' . $overtime_start_time . ':00';
            }
            if (!empty($overtime_end_time)) {
                $update_data['overtime_end_time'] = $work_date . ' ' . $overtime_end_time . ':00';
            }

            if (empty($update_data)) {
                echo json_encode(['success' => false, 'message' => '更新するデータがありません。']);
                return;
            }

            // データ更新
            $this->db->where('attendance_id', $attendance_id);
            $success = $this->db->update('tbl_attendance', $update_data);

            if ($success) {
                echo json_encode(['success' => true, 'message' => '出退勤データが正常に更新されました。']);
            } else {
                echo json_encode(['success' => false, 'message' => '更新に失敗しました。']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'エラー: ' . $e->getMessage()]);
        }
    }

    /**
     * Excel出力API
     */
    public function export_excel()
    {
        try {
            // セッションチェック
            $admin = $this->session->userdata('admin');
            $staff = $this->session->userdata('staff');

            if (empty($admin) && empty($staff)) {
                redirect('/login');
                return;
            }

            $this->load->model('Attendance_model', 'attendance_model');

            $year = $this->input->post('year');
            $month = $this->input->post('month');
            $start_date = $year . '-' . $month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $company_id = $this->input->post('company_id');

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

            // PhpSpreadsheetが利用可能かチェック
            if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                // PhpSpreadsheetが利用できない場合はCSV出力
                $filename = "出退勤一覧_{$year}_{$month}.csv";
                header('Content-Type: text/csv; charset=UTF-8');
                header("Content-Disposition: attachment;filename=\"{$filename}\"");

                echo "\xEF\xBB\xBF"; // UTF-8 BOM

                // ヘッダー行
                $header = ['日付'];
                foreach ($staff_list as $staff_name) {
                    $header[] = $staff_name;
                }
                echo implode(',', $header) . "\n";

                // データ行
                for ($day = 1; $day <= 31; $day++) {
                    $row = ["{$day}日"];

                    foreach ($staff_list as $staff_id => $staff_name) {
                        if (isset($attendance_by_day[$day][$staff_id])) {
                            $d = $attendance_by_day[$day][$staff_id];
                            $text = "出勤:" . date('H:i', strtotime($d['work_time'])) . " ";
                            $text .= "退勤:" . date('H:i', strtotime($d['leave_time'])) . " ";
                            $text .= "休憩:" . $d['break_time'] . "分 ";
                            if (!empty($d['overtime_start_time']) && !empty($d['overtime_end_time'])) {
                                $text .= "残業:" . date('H:i', strtotime($d['overtime_start_time'])) . "～" . date('H:i', strtotime($d['overtime_end_time']));
                            }
                            $row[] = '"' . $text . '"';
                        } else {
                            $row[] = '-';
                        }
                    }
                    echo implode(',', $row) . "\n";
                }
                exit;
            }

            // Excel出力（PhpSpreadsheet利用）
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // ヘッダー行
            $sheet->setCellValue('A1', '日付');
            $colIndex = 2;
            foreach ($staff_list as $staff_name) {
                $sheet->setCellValueByColumnAndRow($colIndex++, 1, $staff_name);
            }

            // データ行
            for ($day = 1; $day <= 31; $day++) {
                $sheet->setCellValueByColumnAndRow(1, $day + 1, "{$day}日");

                $colIndex = 2;
                foreach ($staff_list as $staff_id => $staff_name) {
                    if (isset($attendance_by_day[$day][$staff_id])) {
                        $d = $attendance_by_day[$day][$staff_id];

                        $text = "出勤: " . date('H:i', strtotime($d['work_time'])) . "\n";
                        $text .= "退勤: " . date('H:i', strtotime($d['leave_time'])) . "\n";
                        $text .= "休憩: " . $d['break_time'] . "分\n";
                        if (!empty($d['overtime_start_time']) && !empty($d['overtime_end_time'])) {
                            $text .= "残業: " . date('H:i', strtotime($d['overtime_start_time'])) . "～" . date('H:i', strtotime($d['overtime_end_time']));
                        }

                        $sheet->setCellValueByColumnAndRow($colIndex, $day + 1, $text);
                    }
                    $colIndex++;
                }
            }

            // ダウンロードヘッダーを設定
            $filename = "出退勤一覧_{$year}_{$month}.xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=\"{$filename}\"");
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            echo 'エラー: ' . $e->getMessage();
            exit;
        }
    }

}


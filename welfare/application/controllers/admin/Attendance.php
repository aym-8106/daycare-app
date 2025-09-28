<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Attendance extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        //チャットボット
        $this->header['page'] = 'attendance';
        $this->header['title'] = '出退勤管理';

        $this->load->model('Attendance_model', 'attendance_model');
        $this->load->model('company_model');
        $this->load->model('user_model');
    }

    public function index()
    {
        $this->data['year'] = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $this->data['month'] = isset($_GET['month']) ? $_GET['month'] : date('n');
        $this->data['company_id'] = isset($_GET['moncompany_idth']) ? $_GET['company_id'] : '';

        $this->data['full_month'] = date('m', strtotime(date('Y-') . $this->data['month'] . date('-d')));

        $start_date = $this->data['year'] . '-' . $this->data['full_month'] . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $this->data['days'] = date("t", strtotime($start_date));

        $this->data['company_list'] = $this->company_model->getList("*", array());

        $this->data['company_id'] = isset($_GET['company_id']) ? $_GET['company_id'] : $this->data['company_list'][0]['company_id'];

        // $this->data['month_attendance_data'] = array();
        $this->data['month_attendance_data'] = $this->attendance_model->get_attendance_data_for_admin($start_date, $end_date, $this->data['company_id']);
        // print_R($this->data['month_attendance_data']);die;
        $this->_load_view_admin("admin/attendance/list");
    }

    public function update_attendance()
    {
        try {
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

            // 編集するデータを準備
            $update_data = array();
            if (!empty($work_time)) $update_data['work_time'] = $work_time . ':00';
            if (!empty($leave_time)) $update_data['leave_time'] = $leave_time . ':00';
            if (isset($break_time) && $break_time !== '') $update_data['break_time'] = $break_time;
            if (!empty($overtime_start_time)) $update_data['overtime_start_time'] = $overtime_start_time . ':00';
            if (!empty($overtime_end_time)) $update_data['overtime_end_time'] = $overtime_end_time . ':00';

            if (empty($update_data)) {
                echo json_encode(['success' => false, 'message' => '更新するデータがありません。']);
                return;
            }

            // 基本的な更新処理
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
     * 出退勤編集モーダル用のデータ取得
     */
    public function get_attendance_data()
    {
        try {
            $attendance_id = $this->input->post('attendance_id');

            if (!$attendance_id) {
                echo json_encode(['success' => false, 'message' => 'attendance_idが指定されていません。']);
                return;
            }

            // 基本的なクエリで出退勤データを取得
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
     * 編集ログの表示
     */
    public function view_edit_logs()
    {
        $attendance_id = $this->input->get('attendance_id');
        $staff_id = $this->input->get('staff_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $this->data['edit_logs'] = $this->attendance_model->get_edit_logs($attendance_id, $staff_id, $start_date, $end_date);

        $this->_load_view_admin("admin/attendance/edit_logs");
    }

    public function export_excel()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $start_date = $year . '-' . $month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        $company_id = $this->input->post('company_id');

        // Load attendance data for export (you may already have this logic)
        $data['month_attendance_data'] = $this->attendance_model->get_attendance_data_for_admin($start_date, $end_date, $company_id);

        // Rebuild table structure
        $attendance_by_day = [];
        $staff_list = [];

        foreach ($data['month_attendance_data'] as $record) {
            $day = (int)$record['work_date'];
            $staff_id = $record['staff_id'];
            $attendance_by_day[$day][$staff_id] = $record;

            if (!isset($staff_list[$staff_id])) {
                $staff_list[$staff_id] = $record['staff_name'];
            }
        }

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', '日付');
        $colIndex = 2;
        foreach ($staff_list as $staff_name) {
            $sheet->setCellValueByColumnAndRow($colIndex++, 1, $staff_name);
        }

        // Data rows
        for ($day = 1; $day <= 31; $day++) {
            $sheet->setCellValueByColumnAndRow(1, $day + 1, "{$day}日");

            $colIndex = 2;
            foreach ($staff_list as $staff_id => $staff_name) {
                if (isset($attendance_by_day[$day][$staff_id])) {
                    $d = $attendance_by_day[$day][$staff_id];

                    $text = "出勤: " . date('H:i', strtotime($d['work_time'])) . "\n";
                    $text .= "退勤: " . date('H:i', strtotime($d['leave_time'])) . "\n";
                    $text .= "休憩: " . (($d['total_break_time'] < 60) ? '1分未満' : floor($d['total_break_time'] / 60) . '分') . "\n";
                    $text .= "残業: " . date('H:i', strtotime($d['overtime_start_time'])) . "～" . date('H:i', strtotime($d['overtime_end_time']));

                    $sheet->setCellValueByColumnAndRow($colIndex, $day + 1, $text);
                }
                $colIndex++;
            }
        }

        // Set download headers
        $filename = "出退勤一覧_{$year}_{$month}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

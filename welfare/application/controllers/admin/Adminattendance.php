<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

/**
 * 管理者用出退勤打刻コントローラー
 */
class Adminattendance extends AdminController
{
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->header['page'] = 'admin_attendance';
        $this->header['title'] = '管理画面【管理者用】- 出退勤';

        $this->load->model('Attendance_model', 'attendance_model');
        $this->load->model('staff_model');
    }

    /**
     * 管理者用出退勤打刻画面（事業所の全職員）
     */
    public function index()
    {
        // ログインユーザーの事業所IDを取得
        $user_company_id = isset($this->user['company_id']) ? $this->user['company_id'] : null;
        $today = date("Y-m-d");

        // 同じ事業所の全スタッフを取得
        $all_staff = $this->attendance_model->get_company_staff($user_company_id);

        // 各スタッフの今日の出退勤状況を取得
        $staff_attendance = array();
        foreach ($all_staff as $staff) {
            $attendance = $this->attendance_model->get_admin_today_data($staff['staff_id'], $today);

            $staff_attendance[] = array(
                'staff_id' => $staff['staff_id'],
                'staff_name' => $staff['staff_name'],
                'has_checked_in' => $attendance && !empty($attendance['work_time']) && $attendance['work_time'] !== '0000-00-00 00:00:00',
                'has_checked_out' => $attendance && !empty($attendance['leave_time']) && $attendance['leave_time'] !== '0000-00-00 00:00:00' && $attendance['leave_time'] !== null,
                'work_time' => $attendance && !empty($attendance['work_time']) && $attendance['work_time'] !== '0000-00-00 00:00:00' ? date('H:i', strtotime($attendance['work_time'])) : null,
                'leave_time' => $attendance && !empty($attendance['leave_time']) && $attendance['leave_time'] !== '0000-00-00 00:00:00' && $attendance['leave_time'] !== null ? date('H:i', strtotime($attendance['leave_time'])) : null,
            );
        }

        $this->data = array(
            'today_date' => $today,
            'staff_list' => $staff_attendance,
            'company_id' => $user_company_id
        );

        $this->_load_view_admin("admin/admin_attendance/index");
    }

    /**
     * 出勤打刻
     */
    public function check_in()
    {
        try {
            $staff_id = $this->input->post('staff_id');
            if (empty($staff_id)) {
                echo json_encode(['success' => false, 'message' => 'スタッフIDが指定されていません。']);
                return;
            }

            $today = date("Y-m-d");
            $now = date("Y-m-d H:i:s");

            // 既に出勤済みかチェック
            $existing = $this->attendance_model->get_admin_today_data($staff_id, $today);
            if ($existing && !empty($existing['work_time']) &&
                $existing['work_time'] !== '0000-00-00 00:00:00') {
                echo json_encode(['success' => false, 'message' => '既に出勤打刻済みです。']);
                return;
            }

            $data = array(
                'staff_id' => $staff_id,
                'work_date' => $today,
                'work_time' => $now,
                'create_date' => $now,
                'update_date' => $now
            );

            if ($existing) {
                // 更新
                $this->db->where('attendance_id', $existing['attendance_id']);
                $this->db->where('staff_id', $staff_id);
                $result = $this->db->update('tbl_attendance', array('work_time' => $now, 'update_date' => $now));
            } else {
                // 新規作成
                $result = $this->db->insert('tbl_attendance', $data);
            }

            if ($result) {
                echo json_encode(['success' => true, 'message' => '出勤打刻が完了しました。', 'time' => date('H:i', strtotime($now))]);
            } else {
                echo json_encode(['success' => false, 'message' => '出勤打刻に失敗しました。']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'エラー: ' . $e->getMessage()]);
        }
    }

    /**
     * 退勤打刻
     */
    public function check_out()
    {
        try {
            $staff_id = $this->input->post('staff_id');
            if (empty($staff_id)) {
                echo json_encode(['success' => false, 'message' => 'スタッフIDが指定されていません。']);
                return;
            }

            $today = date("Y-m-d");
            $now = date("Y-m-d H:i:s");

            // 出勤データを取得
            $existing = $this->attendance_model->get_admin_today_data($staff_id, $today);
            if (!$existing || empty($existing['work_time']) || $existing['work_time'] === '0000-00-00 00:00:00') {
                echo json_encode(['success' => false, 'message' => '出勤打刻が必要です。']);
                return;
            }

            // 退勤時刻が既に有効な値で設定されているかを厳密にチェック
            if (!empty($existing['leave_time']) &&
                $existing['leave_time'] !== '0000-00-00 00:00:00' &&
                $existing['leave_time'] !== null) {
                echo json_encode(['success' => false, 'message' => '既に退勤打刻済みです。']);
                return;
            }

            // 退勤時刻を更新
            $this->db->where('attendance_id', $existing['attendance_id']);
            $this->db->where('staff_id', $staff_id);
            $result = $this->db->update('tbl_attendance', array('leave_time' => $now, 'update_date' => $now));

            if ($result) {
                echo json_encode(['success' => true, 'message' => '退勤打刻が完了しました。', 'time' => date('H:i', strtotime($now))]);
            } else {
                echo json_encode(['success' => false, 'message' => '退勤打刻に失敗しました。']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'エラー: ' . $e->getMessage()]);
        }
    }
}
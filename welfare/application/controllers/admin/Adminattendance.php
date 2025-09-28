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
     * 管理者用出退勤打刻画面
     */
    public function index()
    {
        // デバッグ: セッション構造を確認
        // var_dump($this->user); exit;

        // 管理者を仮想的なスタッフとして扱う
        $admin_id = isset($this->user['admin_id']) ? $this->user['admin_id'] : (isset($this->user['userId']) ? $this->user['userId'] : 1);
        $admin_name = isset($this->user['admin_name']) ? $this->user['admin_name'] : (isset($this->user['name']) ? $this->user['name'] : '管理者');

        $admin_as_staff_id = 'admin_' . $admin_id;

        $this->data = array(
            'staff_id' => $admin_as_staff_id,
            'staff_name' => $admin_name,
            'is_admin' => true,
            'today_date' => date("Y-m-d"),
        );

        // 今日の出退勤データを取得
        $attendance = $this->attendance_model->get_admin_today_data($admin_as_staff_id, date("Y-m-d"));

        if ($attendance) {
            $this->data['attendance'] = $attendance;
            $this->data['has_checked_in'] = !empty($attendance['work_time']) &&
                                          $attendance['work_time'] !== '0000-00-00 00:00:00';
            $this->data['has_checked_out'] = !empty($attendance['leave_time']) &&
                                           $attendance['leave_time'] !== '0000-00-00 00:00:00' &&
                                           $attendance['leave_time'] !== null;
        } else {
            $this->data['attendance'] = null;
            $this->data['has_checked_in'] = false;
            $this->data['has_checked_out'] = false;
        }

        $this->_load_view_admin("admin/admin_attendance/index");
    }

    /**
     * 出勤打刻
     */
    public function check_in()
    {
        try {
            $admin_id = isset($this->user['admin_id']) ? $this->user['admin_id'] : (isset($this->user['userId']) ? $this->user['userId'] : 1);
            $admin_as_staff_id = 'admin_' . $admin_id;
            $today = date("Y-m-d");
            $now = date("Y-m-d H:i:s");

            // 既に出勤済みかチェック
            $existing = $this->attendance_model->get_admin_today_data($admin_as_staff_id, $today);
            if ($existing && !empty($existing['work_time']) &&
                $existing['work_time'] !== '0000-00-00 00:00:00') {
                echo json_encode(['success' => false, 'message' => '既に出勤打刻済みです。']);
                return;
            }

            $data = array(
                'staff_id' => $admin_as_staff_id,
                'work_date' => $today,
                'work_time' => $now,
                'create_date' => $now,
                'update_date' => $now
            );

            if ($existing) {
                // 更新
                $this->db->where('attendance_id', $existing['attendance_id']);
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
            $admin_id = isset($this->user['admin_id']) ? $this->user['admin_id'] : (isset($this->user['userId']) ? $this->user['userId'] : 1);
            $admin_as_staff_id = 'admin_' . $admin_id;
            $today = date("Y-m-d");
            $now = date("Y-m-d H:i:s");

            // 出勤データを取得
            $existing = $this->attendance_model->get_admin_today_data($admin_as_staff_id, $today);
            if (!$existing || empty($existing['work_time'])) {
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
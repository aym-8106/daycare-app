<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Attendance_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_attendance';
        $this->primary_key = 'attendance_id';
    }

    function get_today_data($data)
    {
        $this->db->select('BaseTbl.*, Users.name as staff_name');
        $this->db->from('tbl_attendance as BaseTbl');
        $this->db->join('tbl_users as Users', 'Users.userId = BaseTbl.staff_id','left');
        if(!empty($data['staff_id'])) $this->db->where('BaseTbl.staff_id', $data['staff_id']);
        $this->db->where('BaseTbl.work_date', $data['today_date']);
        $query = $this->db->get();

        $result = $query->result();
        return $result;
    }

    /**
     * 管理者用の今日の出退勤データを取得
     */
    function get_admin_today_data($staff_id, $today_date)
    {
        $this->db->select('*');
        $this->db->from('tbl_attendance');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('work_date', $today_date);
        $query = $this->db->get();

        return $query->row_array();
    }

    function insert_working_time($user_id, $work_date, $work_time) 
    {
        $data = array(
            'staff_id'   => $user_id,
            'work_date'  => $work_date,
            'work_time'  => $work_time
        );
    
        $this->db->insert($this->table, $data);
    }

    function update_working_time($user_id, $today_date, $work_time)
    {
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $today_date);
        $this->db->update($this->table, ['work_time' => $work_time]);

        return $this->db->affected_rows();
    }

    function update_leaving_time($user_id, $leave_time, $work_date) 
    {
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $work_date);
        $this->db->update($this->table, ['leave_time' => $leave_time]);

        return $this->db->affected_rows();
    }

    function get_current_break_time($staff_id, $work_date)
    {
        $this->db->select('break_time');
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('work_date', $work_date);
        $query = $this->db->get();
        $result = $query->row_array();
        
        if ($result && $result['break_time']) {
            $start = $result['break_time'];

            return ['break_time' => $start];
        }

        return ['break_time' => $result['break_time']];
    }

    public function save_break_duration($user_id, $work_date, $break_time)
    {
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $work_date);
        $this->db->update($this->table, ['break_time' => $break_time]);
    }

    public function update_reason($user_id, $work_date, $reason_time, $reason_text)
    {
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $work_date);
        $updateData = [
            'overtime_start_time' => $reason_time,
            'overtime_reason' => $reason_text
        ];
    
        $this->db->update($this->table, $updateData);
    }

    public function insert_reason_data($user_id, $work_date, $reason_time, $reason_text)
    {
        $data = array(
            'staff_id'   => $user_id,
            'work_date'  => $work_date,
            'overtime_start_time'  => $reason_time,
            'overtime_reason'  => $reason_text
        );
    
        $this->db->insert($this->table, $data);
    }

    function get_current_overtime_break_time($staff_id, $work_date)
    {
        $this->db->select('overtime_break_time');
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('work_date', $work_date);
        $query = $this->db->get();
        $result = $query->row_array();
        if ($result && $result['overtime_break_time']) {
            $start = $result['overtime_break_time'];

            return ['overtime_break_time' => $start];
        }
        return ['overtime_break_time' => $result['overtime_break_time']];
    }

    public function save_overtime_break_duration($user_id, $work_date, $overtime_break_time)
    {
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $work_date);
        $this->db->update($this->table, ['overtime_break_time' => $overtime_break_time]);
    }

    function update_overwork_end_time($user_id, $overtime_end_time, $work_date) 
    {
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $work_date);
        $this->db->update($this->table, ['overtime_end_time' => $overtime_end_time]);

        return $this->db->affected_rows();
    }

    function get_staff_data($work_date, $user_id)
    {
        $this->db->select('attendance_id');
        $this->db->from($this->table);
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $work_date);
        $query = $this->db->get();
        $result = $query->row_array();
        
        return $result;
    }

    function get_attendance_data($start_date, $end_date, $user_id) {
        $this->db->select("
            DATE_FORMAT(work_date, '%e') AS work_date,
            DATE_FORMAT(work_time, '%H:%i') AS work_time,
            DATE_FORMAT(leave_time, '%H:%i') AS leave_time,
            DATE_FORMAT(overtime_start_time, '%H:%i') AS overtime_start_time,
            DATE_FORMAT(overtime_end_time, '%H:%i') AS overtime_end_time,
            (break_time + overtime_break_time) AS total_break_time,
            TIMEDIFF(overtime_end_time, overtime_start_time) AS overtime_duration,
            Staff.relax_time
        ");
        $this->db->from($this->table);
        $this->db->join('tbl_setting_staff as Staff', 'Staff.staff_id = '.$this->table.'.staff_id','left');
        $this->db->where($this->table.'.staff_id', $user_id);
        $this->db->where('work_date >=', $start_date);
        $this->db->where('work_date <=', $end_date);
        $this->db->order_by('work_date', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }

    function get_attendance_data_for_admin($start_date, $end_date, $company_id) {
        $this->db->select("
            tbl_attendance.attendance_id,
            Company.company_id,
            tbl_attendance.staff_id,
            Users.name as staff_name,
            DATE_FORMAT(work_date, '%e') AS work_date,
            DATE_FORMAT(work_time, '%H:%i') AS work_time,
            DATE_FORMAT(leave_time, '%H:%i') AS leave_time,
            DATE_FORMAT(overtime_start_time, '%H:%i') AS overtime_start_time,
            DATE_FORMAT(overtime_end_time, '%H:%i') AS overtime_end_time,
            (break_time + overtime_break_time) AS total_break_time,
            TIMEDIFF(overtime_end_time, overtime_start_time) AS overtime_duration
        ");
        $this->db->from($this->table);
        $this->db->join('tbl_users as Users', 'Users.userId = '.$this->table.'.staff_id','left');
        $this->db->join('tbl_company as Company', 'Users.company_id = Company.company_id','left');
        $this->db->where('work_date >=', $start_date);
        $this->db->where('work_date <=', $end_date);
        $this->db->where('Users.company_id', $company_id); 
        $this->db->order_by('Company.company_id', 'ASC');
        $this->db->order_by($this->table.'.staff_id', 'ASC');
        $this->db->order_by($this->table.'.work_date', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    

    function get_company_staff($company_id) {
        $this->db->select('userId as staff_id, name as staff_name');
        $this->db->from('tbl_users');
        $this->db->where('company_id', $company_id);
        $this->db->where('roleId >', 1);
        $this->db->where('isDeleted', 0);
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * 特定スタッフの出退勤データを取得
     */
    function get_attendance_data_for_staff($start_date, $end_date, $staff_id) {
        $this->db->select("
            tbl_attendance.attendance_id,
            tbl_attendance.staff_id,
            Users.name as staff_name,
            DATE_FORMAT(work_date, '%e') AS work_date,
            DATE_FORMAT(work_time, '%H:%i') AS work_time,
            DATE_FORMAT(leave_time, '%H:%i') AS leave_time,
            DATE_FORMAT(overtime_start_time, '%H:%i') AS overtime_start_time,
            DATE_FORMAT(overtime_end_time, '%H:%i') AS overtime_end_time,
            (break_time + overtime_break_time) AS total_break_time,
            TIMEDIFF(overtime_end_time, overtime_start_time) AS overtime_duration
        ");
        $this->db->from($this->table);
        $this->db->join('tbl_users as Users', 'Users.userId = '.$this->table.'.staff_id','left');
        $this->db->where('work_date >=', $start_date);
        $this->db->where('work_date <=', $end_date);
        $this->db->where($this->table.'.staff_id', $staff_id);
        $this->db->order_by($this->table.'.work_date', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 拡張された勤怠レコード挿入
     */
    public function insert_attendance_record($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * 拡張された勤怠レコード更新
     */
    public function update_attendance_record($attendance_id, $data)
    {
        $this->db->where('attendance_id', $attendance_id);
        return $this->db->update($this->table, $data);
    }

    /**
     * 拡張された退勤時刻更新
     */
    public function update_leaving_time_extended($user_id, $work_date, $data)
    {
        $this->db->where('staff_id', $user_id);
        $this->db->where('work_date', $work_date);
        return $this->db->update($this->table, $data);
    }

    /**
     * 勤怠修正申請の挿入
     */
    public function insert_correction_request($data)
    {
        return $this->db->insert('tbl_attendance_correction', $data);
    }

    /**
     * 勤怠修正申請の取得
     */
    public function get_correction_requests($staff_id = null, $status = null)
    {
        $this->db->select('
            tbl_attendance_correction.*,
            tbl_users.name as staff_name,
            tbl_attendance.work_date
        ');
        $this->db->from('tbl_attendance_correction');
        $this->db->join('tbl_users', 'tbl_users.userId = tbl_attendance_correction.staff_id', 'left');
        $this->db->join('tbl_attendance', 'tbl_attendance.attendance_id = tbl_attendance_correction.attendance_id', 'left');

        if ($staff_id) {
            $this->db->where('tbl_attendance_correction.staff_id', $staff_id);
        }
        if ($status !== null) {
            $this->db->where('tbl_attendance_correction.status', $status);
        }

        $this->db->order_by('tbl_attendance_correction.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 勤怠修正申請の承認・却下
     */
    public function approve_correction($correction_id, $status, $approved_by, $rejection_reason = null)
    {
        $data = [
            'status' => $status,
            'approved_by' => $approved_by,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($rejection_reason) {
            $data['rejection_reason'] = $rejection_reason;
        }

        $this->db->where('correction_id', $correction_id);
        return $this->db->update('tbl_attendance_correction', $data);
    }

    /**
     * 月次勤怠統計データの取得
     */
    public function get_monthly_statistics($start_date, $end_date, $company_id = null, $staff_id = null)
    {
        $this->db->select("
            tbl_users.userId as staff_id,
            tbl_users.name as staff_name,
            COUNT(tbl_attendance.attendance_id) as work_days,
            SUM(CASE WHEN tbl_attendance.work_time IS NOT NULL THEN 1 ELSE 0 END) as actual_work_days,
            AVG(TIME_TO_SEC(TIMEDIFF(tbl_attendance.leave_time, tbl_attendance.work_time))) / 3600 as avg_work_hours,
            SUM(TIME_TO_SEC(TIMEDIFF(tbl_attendance.overtime_end_time, tbl_attendance.overtime_start_time))) / 3600 as total_overtime_hours,
            SUM(tbl_attendance.break_time) / 60 as total_break_minutes
        ");

        $this->db->from('tbl_users');
        $this->db->join('tbl_attendance', 'tbl_users.userId = tbl_attendance.staff_id', 'left');

        if ($company_id) {
            $this->db->where('tbl_users.company_id', $company_id);
        }
        if ($staff_id) {
            $this->db->where('tbl_users.userId', $staff_id);
        }

        $this->db->where('tbl_attendance.work_date >=', $start_date);
        $this->db->where('tbl_attendance.work_date <=', $end_date);
        $this->db->where('tbl_users.isDeleted', 0);

        $this->db->group_by('tbl_users.userId');
        $this->db->order_by('tbl_users.name');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 勤務予定と実績の比較
     */
    public function get_schedule_vs_actual($start_date, $end_date, $staff_id)
    {
        $this->db->select("
            DATE(calendar.date) as work_date,
            schedule.scheduled_start,
            schedule.scheduled_end,
            attendance.work_time,
            attendance.leave_time,
            CASE
                WHEN attendance.work_time IS NULL THEN '欠勤'
                WHEN TIME(attendance.work_time) > schedule.scheduled_start THEN '遅刻'
                WHEN TIME(attendance.leave_time) < schedule.scheduled_end THEN '早退'
                ELSE '正常'
            END as status
        ");

        // カレンダーテーブル（日付マスタ）があると仮定
        $this->db->from('(SELECT DATE(?) + INTERVAL seq DAY as date FROM
            (SELECT 0 seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION
             SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION
             SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION
             SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION
             SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION
             SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION
             SELECT 30) seq
            WHERE DATE(?) + INTERVAL seq DAY <= ?) calendar', false);

        $this->db->join('tbl_work_schedule schedule',
            'schedule.work_date = calendar.date AND schedule.staff_id = ' . $staff_id, 'left');
        $this->db->join('tbl_attendance attendance',
            'attendance.work_date = calendar.date AND attendance.staff_id = ' . $staff_id, 'left');

        $query = $this->db->get(null, null, null, false);
        $query = $this->db->query($query, [$start_date, $start_date, $end_date]);

        return $query->result_array();
    }

    /**
     * 異常勤怠の検出
     */
    public function detect_anomalies($start_date, $end_date, $company_id)
    {
        $anomalies = [];

        // 長時間労働の検出（12時間以上）
        $this->db->select('
            tbl_attendance.*,
            tbl_users.name as staff_name,
            TIMEDIFF(leave_time, work_time) as work_duration
        ');
        $this->db->from('tbl_attendance');
        $this->db->join('tbl_users', 'tbl_users.userId = tbl_attendance.staff_id');
        $this->db->where('tbl_users.company_id', $company_id);
        $this->db->where('tbl_attendance.work_date >=', $start_date);
        $this->db->where('tbl_attendance.work_date <=', $end_date);
        $this->db->where('TIME_TO_SEC(TIMEDIFF(leave_time, work_time)) > 43200'); // 12時間
        $this->db->where('tbl_attendance.work_time IS NOT NULL');
        $this->db->where('tbl_attendance.leave_time IS NOT NULL');

        $long_work = $this->db->get()->result_array();

        foreach ($long_work as $record) {
            $anomalies[] = [
                'type' => 'long_work',
                'staff_name' => $record['staff_name'],
                'work_date' => $record['work_date'],
                'description' => '長時間労働: ' . $record['work_duration'],
                'severity' => 'high'
            ];
        }

        return $anomalies;
    }

    /**
     * 出退勤データの編集（ログ付き）
     */
    public function update_attendance_with_log($attendance_id, $new_data, $edited_by, $edit_reason = '')
    {
        // 現在のデータを取得
        $this->db->where('attendance_id', $attendance_id);
        $current_data = $this->db->get($this->table)->row_array();

        if (!$current_data) {
            return false;
        }

        // 変更されたフィールドを特定してログに記録
        $log_entries = [];
        $trackable_fields = [
            'work_time' => '出勤時間',
            'leave_time' => '退勤時間',
            'break_time' => '休憩時間',
            'overtime_start_time' => '残業開始時間',
            'overtime_end_time' => '残業終了時間'
        ];

        foreach ($trackable_fields as $field => $field_name) {
            if (isset($new_data[$field]) && $new_data[$field] != $current_data[$field]) {
                $log_entries[] = [
                    'attendance_id' => $attendance_id,
                    'staff_id' => $current_data['staff_id'],
                    'work_date' => $current_data['work_date'],
                    'field_name' => $field_name,
                    'old_value' => $current_data[$field],
                    'new_value' => $new_data[$field],
                    'edited_by' => $edited_by,
                    'edit_reason' => $edit_reason,
                    'edited_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        // トランザクション開始
        $this->db->trans_start();

        // 出退勤データを更新
        $this->db->where('attendance_id', $attendance_id);
        $this->db->update($this->table, $new_data);

        // ログを挿入
        if (!empty($log_entries)) {
            $this->db->insert_batch('tbl_attendance_edit_log', $log_entries);
        }

        // トランザクション終了
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * 出退勤編集ログの取得
     */
    public function get_edit_logs($attendance_id = null, $staff_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select('
            log.*,
            users.name as staff_name
        ');
        $this->db->from('tbl_attendance_edit_log log');
        $this->db->join('tbl_users users', 'users.userId = log.staff_id', 'left');

        if ($attendance_id) {
            $this->db->where('log.attendance_id', $attendance_id);
        }
        if ($staff_id) {
            $this->db->where('log.staff_id', $staff_id);
        }
        if ($start_date) {
            $this->db->where('log.work_date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('log.work_date <=', $end_date);
        }

        $this->db->order_by('log.edited_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 特定の出退勤レコードの詳細情報を取得（編集用）
     */
    public function get_attendance_for_edit($attendance_id)
    {
        $this->db->select('
            att.*,
            users.name as staff_name,
            users.userId as staff_number,
            company.company_name
        ');
        $this->db->from($this->table . ' att');
        $this->db->join('tbl_users users', 'users.userId = att.staff_id', 'left');
        $this->db->join('tbl_company company', 'company.company_id = users.company_id', 'left');
        $this->db->where('att.attendance_id', $attendance_id);

        $query = $this->db->get();
        return $query->row_array();
    }
}
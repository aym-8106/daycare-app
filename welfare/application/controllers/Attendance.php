<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class Attendance extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);


        //チャットボット
        $this->header['page'] = 'attendance';
        $this->header['title'] = 'CareNavi訪問看護';
        $this->header['user'] = $this->user;
        
        $this->load->model('Attendance_model', 'attendance_model');
        $this->load->model('user_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->data = array(
            'staff_id' => $this->user['staff_id'],
            'today_date' => date("Y-m-d"),
        );

        $attendance = $this->attendance_model->get_today_data($this->data);

        // Check if attendance data is available
        if (!empty($attendance)) {
            $row = $attendance[0]; // easier to read
            
            $work_time = ($row->work_time != '0000-00-00 00:00:00') 
                ? date('Y-m-d H:i', strtotime($row->work_time)) 
                : $row->work_time;

            $leave_time = ($row->leave_time != '0000-00-00 00:00:00') 
                ? date('Y-m-d H:i', strtotime($row->leave_time)) 
                : $row->leave_time;

            $overtime_start_time = ($row->overtime_start_time != '0000-00-00 00:00:00') 
                ? date('Y-m-d H:i', strtotime($row->overtime_start_time)) 
                : $row->overtime_start_time;

            $overtime_end_time = ($row->overtime_end_time != '0000-00-00 00:00:00') 
                ? date('Y-m-d H:i', strtotime($row->overtime_end_time)) 
                : $row->overtime_end_time;

            $this->data['attendance_data'] = array(
                'attendance_id'         => $row->attendance_id,
                'staff_id'              => $row->staff_id,
                'work_date'             => $row->work_date,
                'work_time'             => $work_time,
                'leave_time'            => $leave_time,
                'break_time'            => $row->break_time,
                'overtime_start_time'   => $overtime_start_time,
                'overtime_end_time'     => $overtime_end_time,
                'overtime_break_time'   => $row->overtime_break_time,
                'overtime_reason'       => $row->overtime_reason,
            );
        } else {
            // Set attendance_data to empty or null safely
            $this->data['attendance_data'] = null;
        }
        $this->_load_view("attendance/index");
    }

    public function overtime()
    {
        $this->data['start_time'] = $this->input->post('start_time') ?: date('00:00');
        $this->data['end_time'] = $this->input->post('end_time') ?: date('00:00');
        $this->data['relax_time'] = $this->input->post('relax_time') ?: date('00:00');
        $this->data['reason'] = $this->input->post('reason') ?: "";

        $this->_load_view("attendance/overtime");
    }

    public function list()
    {
        $this->data['year'] = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $this->data['month'] = isset($_GET['month']) ? $_GET['month'] : date('n');
        
        $this->data['full_month'] = date('m', strtotime(date('Y-').$this->data['month'].date('-d')));
        
        $start_date = $this->data['year'] . '-' . $this->data['full_month'] . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $this->data['days'] = date("t", strtotime($start_date));

        // Check user role
        if ($this->user['staff_role'] == 1) {
            $company_id = $this->user['company_id'];
            
            $this->data['company_staff'] = $this->attendance_model->get_company_staff($company_id);
            
            $user_id = isset($_GET['staff_id']) ? $_GET['staff_id'] : $this->data['company_staff'][0]['staff_id'];
            $this->data['selected_staff_id'] = $user_id;

            $this->data['month_attendance_data'] = $this->attendance_model->get_attendance_data($start_date, $end_date, $user_id);
            
        } else if ($this->user['staff_role'] == 2) {
            $user_id = $this->user['staff_id'];
            $this->data['selected_staff_id'] = $user_id;
            $this->data['month_attendance_data'] = $this->attendance_model->get_attendance_data($start_date, $end_date, $user_id);
        }

        $this->_load_view('attendance/list');
    }

    public function insert_work_time()
    {
        $user_id = $this->user['staff_id'];
        $user_name = $this->user['staff_name'];
        $work_time = $this->input->post('work_time');
        $location = $this->input->post('location');
        $memo = $this->input->post('memo');

        if (empty($work_time)) {
            echo json_encode([
                'success' => false,
                'message' => '勤務開始時刻が無効です。'
            ]);
            return;
        }

        // 位置情報の検証（オプション）
        if ($this->_location_validation_required() && empty($location)) {
            echo json_encode([
                'success' => false,
                'message' => '勤務場所の確認が必要です。位置情報を有効にしてください。'
            ]);
            return;
        }

        $work_date = date('Y-m-d', strtotime($work_time));
        $existing_data = $this->attendance_model->get_staff_data($work_date, $user_id);

        // 写真アップロード処理
        $photo_path = null;
        if (isset($_FILES['check_in_photo']) && $_FILES['check_in_photo']['size'] > 0) {
            $photo_path = $this->_handle_photo_upload($_FILES['check_in_photo'], 'checkin_' . $user_id . '_' . date('Ymd_His'));
        }

        $update_data = [
            'work_time' => $work_time,
            'location' => $location,
            'memo' => $memo,
            'check_in_photo' => $photo_path
        ];

        if (!empty($existing_data) && isset($existing_data['attendance_id'])) {
            $this->attendance_model->update_attendance_record($existing_data['attendance_id'], $update_data);
        } else {
            $insert_data = array_merge($update_data, [
                'staff_id' => $user_id,
                'work_date' => $work_date
            ]);
            $this->attendance_model->insert_attendance_record($insert_data);
        }

        $work_time_view = date('Y-m-d H:i', strtotime($work_time));
        echo json_encode([
            'success' => true,
            'work_time' => $work_time_view,
            'user_name' => $user_name,
            'location' => $location,
            'photo_uploaded' => $photo_path ? true : false
        ]);
    }

    public function update_leave_time ()
    {
        $user_id = $this->user['staff_id'];
        $leave_time = $this->input->post('leave_time');
        $user_name = $this->user['staff_name'];
        $location = $this->input->post('location');
        $memo = $this->input->post('memo');
        $work_date = date('Y-m-d', strtotime($leave_time));

        // 写真アップロード処理
        $photo_path = null;
        if (isset($_FILES['check_out_photo']) && $_FILES['check_out_photo']['size'] > 0) {
            $photo_path = $this->_handle_photo_upload($_FILES['check_out_photo'], 'checkout_' . $user_id . '_' . date('Ymd_His'));
        }

        $update_data = [
            'leave_time' => $leave_time,
            'location' => $location,
            'memo' => $memo,
            'check_out_photo' => $photo_path
        ];

        $this->attendance_model->update_leaving_time_extended($user_id, $work_date, $update_data);

        echo json_encode([
            'success' => true,
            'leave_time' => date('Y-m-d H:i', strtotime($leave_time)),
            'user_name' => $user_name,
            'location' => $location,
            'photo_uploaded' => $photo_path ? true : false
        ]);
    }

    public function get_break_time()
    {
        $staff_id = $this->user['staff_id'];
        $work_date = $this->input->post('work_date');
        $user_name = $this->user['staff_name'];
        $data = $this->attendance_model->get_current_break_time($staff_id, $work_date);
        echo json_encode([
            'success' => true,
            'break_time' => $data['break_time'],
            'user_name' => $user_name,
        ]);
    }

    public function end_break_time()
    {
        $break_time = $this->input->post('break_duration');
        $user_id = $this->user['staff_id'];
        $work_date = date('Y-m-d');

        // Save to DB (implement this method in your model)
        $this->attendance_model->save_break_duration($user_id, $work_date, $break_time);

        echo json_encode(['success' => true]);
    }

    public function insert_reason()
    {
        $reason_time = date('Y-m-d')." ".$this->input->post('reason_time').date(':s');
        $reason_text = $this->input->post('reason_text');
        $work_date = date('Y-m-d');
        $user_id = $this->user['staff_id'];

        $result = $this->data['attendance_data'] = $this->attendance_model->get_staff_data($work_date, $user_id);

        if($result) {
            $this->attendance_model->update_reason($user_id, $work_date, $reason_time, $reason_text);
        } else {
            $this->attendance_model->insert_reason_data($user_id, $work_date, $reason_time, $reason_text);
        }

        $over_time = $this->input->post('reason_time');
        echo json_encode([
            'success' => true,
            'over_time' => $over_time,
        ]);
    }

    public function get_overtime_break_time()
    {
        $staff_id = $this->user['staff_id'];
        $work_date = $this->input->post('work_date');
        $data = $this->attendance_model->get_current_overtime_break_time($staff_id, $work_date);
        $user_name = $this->user['staff_name'];

        echo json_encode([
            'success' => true,
            'overtime_break_time' => $data['overtime_break_time'],
            'user_name' => $user_name,
        ]);
    }

    public function overtime_end_break_time()
    {
        $overtime_break_time = $this->input->post('break_duration1');
        $user_id = $this->user['staff_id'];
        $work_date = date('Y-m-d');
        // Save to DB (implement this method in your model)
        $this->attendance_model->save_overtime_break_duration($user_id, $work_date, $overtime_break_time);

        echo json_encode(['success' => true]);
    }

    public function update_overtime_end_time () 
    {
        $user_id = $this->user['staff_id'];
        $overtime_end_time = $this->input->post('overtime_end_time');
        $work_date = date('Y-m-d', strtotime($overtime_end_time));
        
        $this->attendance_model->update_overwork_end_time($user_id, $overtime_end_time, $work_date);

        echo json_encode([
            'success' => true
        ]);
    }

    /**
     * 位置情報の検証が必要かチェック
     */
    private function _location_validation_required()
    {
        // 設定から位置情報検証の必要性をチェック
        // 今回は簡単に false を返すが、実際は設定テーブルから取得
        return false;
    }

    /**
     * 写真のアップロード処理
     */
    private function _handle_photo_upload($file, $filename_prefix)
    {
        $config['upload_path'] = './uploads/attendance_photos/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048; // 2MB
        $config['file_name'] = $filename_prefix . '_' . uniqid();

        // ディレクトリが存在しない場合は作成
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('check_in_photo') || $this->upload->do_upload('check_out_photo')) {
            $upload_data = $this->upload->data();
            return 'uploads/attendance_photos/' . $upload_data['file_name'];
        } else {
            log_message('error', 'Photo upload failed: ' . $this->upload->display_errors());
            return null;
        }
    }

    /**
     * GPS情報から住所を取得（Google Maps API等を使用）
     */
    private function _get_address_from_coordinates($latitude, $longitude)
    {
        // 実際の実装ではGoogle Maps Geocoding API等を使用
        return "緯度: {$latitude}, 経度: {$longitude}";
    }

    /**
     * 勤怠修正申請
     */
    public function request_correction()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('attendance_id', '勤怠ID', 'required|integer');
        $this->form_validation->set_rules('correction_type', '修正種別', 'required|integer');
        $this->form_validation->set_rules('corrected_value', '修正後の値', 'required');
        $this->form_validation->set_rules('reason', '修正理由', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            return;
        }

        $data = [
            'attendance_id' => $this->input->post('attendance_id'),
            'staff_id' => $this->user['staff_id'],
            'correction_type' => $this->input->post('correction_type'),
            'original_value' => $this->input->post('original_value'),
            'corrected_value' => $this->input->post('corrected_value'),
            'reason' => $this->input->post('reason'),
            'status' => 0 // 申請中
        ];

        if ($this->attendance_model->insert_correction_request($data)) {
            echo json_encode([
                'success' => true,
                'message' => '修正申請を送信しました。'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '修正申請の送信に失敗しました。'
            ]);
        }
    }

    /**
     * QRコードを使った打刻
     */
    public function qr_punch()
    {
        $qr_code = $this->input->post('qr_code');
        $punch_type = $this->input->post('punch_type'); // 'check_in' or 'check_out'

        // QRコードの検証
        if (!$this->_validate_qr_code($qr_code)) {
            echo json_encode([
                'success' => false,
                'message' => '無効なQRコードです。'
            ]);
            return;
        }

        $user_id = $this->user['staff_id'];
        $current_time = date('Y-m-d H:i:s');
        $work_date = date('Y-m-d');

        if ($punch_type == 'check_in') {
            $this->insert_work_time();
        } else {
            $this->update_leave_time();
        }
    }

    /**
     * QRコードの検証
     */
    private function _validate_qr_code($qr_code)
    {
        // 実際の実装では、QRコードの内容を検証
        // 例：事業所固有の文字列、有効期限等をチェック
        return !empty($qr_code);
    }
}

?>
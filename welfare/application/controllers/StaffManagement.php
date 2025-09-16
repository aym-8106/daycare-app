<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';

class StaffManagement extends UserController
{
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->header['page'] = 'staff_management';
        $this->header['title'] = '職員管理 - CareNavi';
        $this->header['user'] = $this->user;

        $this->load->model('Staff_model', 'staff_model');
        $this->load->model('Company_model', 'company_model');
        $this->load->model('Jobtype_model', 'jobtype_model');
        $this->load->model('Employtype_model', 'employtype_model');
        $this->load->model('Role_model', 'role_model');
    }

    /**
     * 職員一覧画面
     */
    public function index()
    {
        $searchText = $this->input->get('search', TRUE);
        $company_id = $this->input->get('company_id', TRUE);
        $jobtype_id = $this->input->get('jobtype_id', TRUE);
        $status = $this->input->get('status', TRUE);

        $page = $this->input->get('page', TRUE) ?: 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = [];
        if ($searchText) $where['searchText'] = $searchText;
        if ($company_id) $where['BaseTbl.company_id'] = $company_id;
        if ($jobtype_id) $where['BaseTbl.staff_jobtype'] = $jobtype_id;
        if ($status !== null && $status !== '') $where['BaseTbl.status'] = $status;

        // 管理者の場合は自社のみ表示
        if ($this->user['staff_role'] == ROLE_ADMIN) {
            $where['BaseTbl.company_id'] = $this->user['company_id'];
        }

        $this->data['staff_list'] = $this->staff_model->getList('', $where, false, $limit, $offset);
        $this->data['total_count'] = $this->staff_model->getList('', $where, true);
        $this->data['companies'] = $this->company_model->getList('company_id, company_name', ['del_flag' => 0]);
        $this->data['job_types'] = $this->jobtype_model->getAll();
        $this->data['roles'] = $this->role_model->getAll();

        // ページネーション
        $this->data['current_page'] = $page;
        $this->data['total_pages'] = ceil($this->data['total_count'] / $limit);
        $this->data['search_params'] = [
            'search' => $searchText,
            'company_id' => $company_id,
            'jobtype_id' => $jobtype_id,
            'status' => $status
        ];

        $this->_load_view('staff_management/index');
    }

    /**
     * 職員詳細画面
     */
    public function detail($staff_id)
    {
        if (!$staff_id) {
            redirect('staff_management');
        }

        $staff = $this->staff_model->getById($staff_id);
        if (!$staff || $staff['del_flag'] == 1) {
            show_404();
        }

        // 権限チェック：管理者は自社の職員のみ閲覧可能
        if ($this->user['staff_role'] == ROLE_ADMIN &&
            $staff['company_id'] != $this->user['company_id']) {
            show_error('権限がありません。', 403);
        }

        $this->data['staff'] = $staff;
        $this->data['companies'] = $this->company_model->getList('company_id, company_name', ['del_flag' => 0]);
        $this->data['job_types'] = $this->jobtype_model->getAll();
        $this->data['employ_types'] = $this->employtype_model->getAll();
        $this->data['roles'] = $this->role_model->getAll();

        $this->_load_view('staff_management/detail');
    }

    /**
     * 職員新規登録画面
     */
    public function create()
    {
        $this->data['companies'] = $this->company_model->getList('company_id, company_name', ['del_flag' => 0]);
        $this->data['job_types'] = $this->jobtype_model->getAll();
        $this->data['employ_types'] = $this->employtype_model->getAll();
        $this->data['roles'] = $this->role_model->getAll();

        $this->_load_view('staff_management/create');
    }

    /**
     * 職員編集画面
     */
    public function edit($staff_id)
    {
        if (!$staff_id) {
            redirect('staff_management');
        }

        $staff = $this->staff_model->getById($staff_id);
        if (!$staff || $staff['del_flag'] == 1) {
            show_404();
        }

        // 権限チェック
        if ($this->user['staff_role'] == ROLE_ADMIN &&
            $staff['company_id'] != $this->user['company_id']) {
            show_error('権限がありません。', 403);
        }

        $this->data['staff'] = $staff;
        $this->data['companies'] = $this->company_model->getList('company_id, company_name', ['del_flag' => 0]);
        $this->data['job_types'] = $this->jobtype_model->getAll();
        $this->data['employ_types'] = $this->employtype_model->getAll();
        $this->data['roles'] = $this->role_model->getAll();

        $this->_load_view('staff_management/edit');
    }

    /**
     * 職員登録処理
     */
    public function store()
    {
        $this->load->library('form_validation');

        // バリデーションルール
        $this->form_validation->set_rules('staff_name', '職員名', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('staff_mail_address', 'メールアドレス', 'required|valid_email|max_length[255]|is_unique[tbl_staff.staff_mail_address]');
        $this->form_validation->set_rules('staff_password', 'パスワード', 'required|min_length[8]');
        $this->form_validation->set_rules('company_id', '事業所', 'required|integer');
        $this->form_validation->set_rules('staff_jobtype', '職種', 'required|integer');
        $this->form_validation->set_rules('staff_employtype', '雇用形態', 'required|integer');
        $this->form_validation->set_rules('staff_role', '権限', 'required|integer');
        $this->form_validation->set_rules('staff_phone', '電話番号', 'trim|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('staff_management/create');
        }

        $data = [
            'staff_name' => $this->input->post('staff_name'),
            'staff_mail_address' => $this->input->post('staff_mail_address'),
            'staff_password' => password_hash($this->input->post('staff_password'), PASSWORD_DEFAULT),
            'company_id' => $this->input->post('company_id'),
            'staff_jobtype' => $this->input->post('staff_jobtype'),
            'staff_employtype' => $this->input->post('staff_employtype'),
            'staff_role' => $this->input->post('staff_role'),
            'staff_phone' => $this->input->post('staff_phone'),
            'hire_date' => $this->input->post('hire_date'),
            'status' => 1, // アクティブ
            'created_by' => $this->user['staff_id'],
            'create_date' => date('Y-m-d H:i:s')
        ];

        if ($this->staff_model->insert($data)) {
            $this->session->set_flashdata('success', '職員を登録しました。');
            redirect('staff_management');
        } else {
            $this->session->set_flashdata('error', '職員の登録に失敗しました。');
            redirect('staff_management/create');
        }
    }

    /**
     * 職員更新処理
     */
    public function update($staff_id)
    {
        if (!$staff_id) {
            redirect('staff_management');
        }

        $staff = $this->staff_model->getById($staff_id);
        if (!$staff) {
            show_404();
        }

        $this->load->library('form_validation');

        // バリデーションルール
        $this->form_validation->set_rules('staff_name', '職員名', 'required|trim|max_length[100]');
        $email_rule = ($this->input->post('staff_mail_address') != $staff['staff_mail_address'])
            ? 'required|valid_email|max_length[255]|is_unique[tbl_staff.staff_mail_address]'
            : 'required|valid_email|max_length[255]';
        $this->form_validation->set_rules('staff_mail_address', 'メールアドレス', $email_rule);

        if ($this->input->post('staff_password')) {
            $this->form_validation->set_rules('staff_password', 'パスワード', 'min_length[8]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('staff_management/edit/' . $staff_id);
        }

        $data = [
            'staff_name' => $this->input->post('staff_name'),
            'staff_mail_address' => $this->input->post('staff_mail_address'),
            'company_id' => $this->input->post('company_id'),
            'staff_jobtype' => $this->input->post('staff_jobtype'),
            'staff_employtype' => $this->input->post('staff_employtype'),
            'staff_role' => $this->input->post('staff_role'),
            'staff_phone' => $this->input->post('staff_phone'),
            'hire_date' => $this->input->post('hire_date'),
            'status' => $this->input->post('status'),
            'updated_by' => $this->user['staff_id'],
            'update_date' => date('Y-m-d H:i:s')
        ];

        // パスワードが入力されている場合のみ更新
        if ($this->input->post('staff_password')) {
            $data['staff_password'] = password_hash($this->input->post('staff_password'), PASSWORD_DEFAULT);
        }

        if ($this->staff_model->update($staff_id, $data)) {
            $this->session->set_flashdata('success', '職員情報を更新しました。');
            redirect('staff_management/detail/' . $staff_id);
        } else {
            $this->session->set_flashdata('error', '職員情報の更新に失敗しました。');
            redirect('staff_management/edit/' . $staff_id);
        }
    }

    /**
     * 職員削除処理（論理削除）
     */
    public function delete($staff_id)
    {
        if (!$staff_id) {
            redirect('staff_management');
        }

        $staff = $this->staff_model->getById($staff_id);
        if (!$staff) {
            show_404();
        }

        // 権限チェック
        if ($this->user['staff_role'] == ROLE_ADMIN &&
            $staff['company_id'] != $this->user['company_id']) {
            show_error('権限がありません。', 403);
        }

        $data = [
            'del_flag' => 1,
            'updated_by' => $this->user['staff_id'],
            'update_date' => date('Y-m-d H:i:s')
        ];

        if ($this->staff_model->update($staff_id, $data)) {
            $this->session->set_flashdata('success', '職員を削除しました。');
        } else {
            $this->session->set_flashdata('error', '職員の削除に失敗しました。');
        }

        redirect('staff_management');
    }

    /**
     * 職員のインポート（CSVアップロード）
     */
    public function import()
    {
        if ($_FILES['csv_file']['size'] == 0) {
            $this->session->set_flashdata('error', 'CSVファイルを選択してください。');
            redirect('staff_management');
        }

        $config['upload_path'] = './uploads/temp/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = 2048; // 2MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('csv_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('staff_management');
        }

        $data = $this->upload->data();
        $file_path = $data['full_path'];

        // CSV処理
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            $header = fgetcsv($handle);
            $success_count = 0;
            $error_count = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== FALSE) {
                $staff_data = [
                    'staff_name' => $row[0],
                    'staff_mail_address' => $row[1],
                    'staff_password' => password_hash($row[2], PASSWORD_DEFAULT),
                    'company_id' => $row[3],
                    'staff_jobtype' => $row[4],
                    'staff_employtype' => $row[5],
                    'staff_role' => $row[6],
                    'staff_phone' => $row[7] ?? '',
                    'hire_date' => $row[8] ?? null,
                    'status' => 1,
                    'created_by' => $this->user['staff_id'],
                    'create_date' => date('Y-m-d H:i:s')
                ];

                if ($this->staff_model->insert($staff_data)) {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = "行" . ($success_count + $error_count + 1) . ": " . $staff_data['staff_name'];
                }
            }
            fclose($handle);
        }

        // 一時ファイル削除
        unlink($file_path);

        if ($success_count > 0) {
            $this->session->set_flashdata('success', "{$success_count}件の職員を登録しました。");
        }
        if ($error_count > 0) {
            $this->session->set_flashdata('warning', "{$error_count}件の登録に失敗しました。");
        }

        redirect('staff_management');
    }

    /**
     * 職員のエクスポート（CSV出力）
     */
    public function export()
    {
        $where = ['BaseTbl.company_id' => $this->user['company_id']];
        $staff_list = $this->staff_model->getList('', $where);

        $filename = '職員一覧_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=shift_jis');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // ヘッダー行
        $headers = ['職員名', 'メールアドレス', '事業所名', '職種', '雇用形態', '権限', '電話番号', '入社日', 'ステータス'];
        fputcsv($output, $headers);

        // データ行
        foreach ($staff_list as $staff) {
            $row = [
                $staff['staff_name'],
                $staff['staff_mail_address'],
                $staff['company_name'],
                $staff['jobtype'],
                $staff['employtype'],
                $staff['role'],
                $staff['staff_phone'],
                $staff['hire_date'],
                $staff['status'] == 1 ? 'アクティブ' : '非アクティブ'
            ];

            // Shift_JISに変換
            $row = array_map(function($item) {
                return mb_convert_encoding($item, 'SJIS', 'UTF-8');
            }, $row);

            fputcsv($output, $row);
        }

        fclose($output);
    }
}
?>
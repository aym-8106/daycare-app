<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UserController.php';
require_once(APPPATH . '../vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;

class Instruction extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        //チャットボット
        $this->header['page'] = 'instruction';
        $this->header['title'] = 'CareNavi訪問看護';
        $this->header['user'] = $this->user;

        $this->load->model('user_model');
        $this->load->model('instruction_model');
        $this->load->model('patient_model');
        $this->load->model('company_model');
        $this->load->model('staff_model');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        $staff_id = $this->user['staff_id'];
        $this->data['today'] = date('Y-m-d');

        
        $this->data['start_date'] = $this->input->post('start_date');
        $this->data['end_date'] = $this->input->post('end_date');

        if($this->data['start_date'] && $this->data['end_date']) {
            $this->data['start_date'] = date('Y-m-d', strtotime($this->data['start_date']));
            $this->data['end_date'] = date('Y-m-d', strtotime($this->data['end_date']));
        } else {
            $this->data['start_date'] = date('Y-m-01', strtotime($this->data['today']));
            $this->data['end_date'] = date('Y-m-t', strtotime($this->data['today'])); 
        }

        if ($mode == 'update') {
            $use_flag = $this->input->post('use_flag');
            $id = $this->input->post('company_id');
            $data = array(
                'use_flag' => $use_flag,
                'company_id' => $id,
            );
            $this->instruction_model->saveSetting($data);
        }
        $this->data['search'] = $this->input->post('searchText');
        
        $this->load->library('pagination');
        
        $this->data['list_cnt'] = $this->instruction_model->get_total_List('*', $this->data['search'], $this->data['start_date'], $this->data['end_date'], true);
        $returns = $this->_paginationCompress("instruction/index", $this->data['list_cnt'], 10);

        $this->data['start_page'] = $returns["segment"] + 1;
        $this->data['end_page'] = $returns["segment"] + $returns["page"];
        if ($this->data['end_page'] > $this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if (!$this->data['start_page']) $this->data['start_page'] = 1;

        $this->data['list'] = $this->instruction_model->get_total_List('tbl_instruction.*, tbl_instruction.id as instruction_id, tbl_patient.id as patient_id, tbl_patient.patient_name, tbl_staff.staff_name, tbl_company.company_name', $this->data['search'], $this->data['start_date'], $this->data['end_date'], false, $returns['page'], $returns['segment']);


        $this->_load_view("instruction/index");
    }

    /**
     * This function is used to load the user list
     */
    function add()
    {
        $mode = $this->input->post('mode');
        $this->data['patient'] = $this->patient_model->get_all_data();
        $this->data['company'] = $this->company_model->get_all_data();

        if($mode == 'copy') {
            $this->data['instructionId'] = $this->input->post('instructionId');
            $instruction = $this->instruction_model->get_form_data($this->data['instructionId']);

            $company_id = $instruction['company_id'];
            $this->data['staffList'] = $this->staff_model->get_staff($company_id);

            $this->data['instruction'] = array(
                'id' => $instruction['id'],
                'patient_id' => $instruction['patient_id'],
                'patient_name' => $instruction['patient_name'],
                'company_id' => $instruction['company_id'],
                'company_name' => $instruction['company_name'],
                'instruction_start' => $instruction['instruction_start'],
                'instruction_end' => $instruction['instruction_end'],
                'staff_id' => $instruction['staff_id'],
                'staff_name' => $instruction['staff_name']
            );
        } else {
            $this->data['staff_id'] = $this->user['staff_id'];
            $this->data['staff_name'] = $this->user['staff_name'];
            $this->data['company_id'] = $this->user['company_id'];
            $company_data = $this->company_model->getSetting($this->data['company_id']);
            $this->data['company_name'] = $company_data['company_name'];
            
            if ($this->form_validation->run() === TRUE) {
                if($this->data['staff_id'] != 0 && $this->data['company_id'] != 0) {
                    if ($mode == 'save') {
                        $result = $this->patient_model->patient_add($this->data['patient']);
                    } else if($mode == 'update') {
                        $id = $this->input->post('patient_id');
                        $result = $this->patient_model->patient_update($id, $this->data['patient']);
                    }
                    redirect('instruction');
                } else {
                    
                }
            } else {
    //                var_dump(validation_errors());;
            }
        }

        $this->_load_view("instruction/add");
    }

    public function edit()
    {
        $mode = $this->input->post('mode');
        $this->data['instructionId'] = $this->input->post('instructionId');

        $this->data['patient'] = $this->patient_model->get_all_data();
        $this->data['company'] = $this->company_model->get_all_data();

        $instruction = $this->instruction_model->get_form_data($this->data['instructionId']);

        $company_id = $instruction['company_id'];
        $this->data['staff_list'] = $this->staff_model->get_staff($company_id);

        if (empty($instruction)) {
            redirect('instruction');
        }

        if ($mode == 'edit') {
            $this->data['instruction'] = array(
                'id' => $instruction['id'],
                'patient_id' => $instruction['patient_id'],
                'patient_name' => $instruction['patient_name'],
                'company_id' => $instruction['company_id'],
                'company_name' => $instruction['company_name'],
                'instruction_start' => $instruction['instruction_start'],
                'instruction_end' => $instruction['instruction_end'],
                'staff_id' => $instruction['staff_id'],
                'staff_name' => $instruction['staff_name']
            );
        } else {
            redirect('instruction');
        }

        $this->_load_view("instruction/edit");
    }

    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function delete()
    {
        $id = $this->input->post('instructionId');
        $mode = $this->input->post('mode');

        if($mode == 'delete') {
            $result = $this->instruction_model->instruction_delete($id);
            redirect('instruction');
        }
    }

    public function get_patient_data() {
        // $id = $this->input->post('id');
        // $patient = $this->patient_model->getFromId($id);
    
        // // Mapping for readable names
        // $weekdays = [1 => '月曜日', 2 => '火曜日', 3 => '水曜日', 4 => '木曜日', 5 => '金曜日', 6 => '土曜日', 7 => '日曜日'];
        // $curetype = [1 => '看護', 2 => 'リハビリ'];
        // $repeat = [1 => '毎日', 2 => '毎週', 3 => '隔週', 4 => '毎月'];
    
        // echo json_encode([
        //     'patient_addr' => $patient['patient_addr'],
        //     'patient_date' => $patient['patient_date'],
        //     'patient_date_name' => $weekdays[$patient['patient_date']] ?? '',
        //     'patient_curetype' => $patient['patient_curetype'],
        //     'patient_curetype_name' => $curetype[$patient['patient_curetype']] ?? '',
        //     'patient_usefrom' => $patient['patient_usefrom'],
        //     'patient_useto' => $patient['patient_useto'],
        //     'patient_repeat' => $patient['patient_repeat'],
        //     'patient_repeat_name' => $repeat[$patient['patient_repeat']] ?? ''
        // ]);
    }

    public function get_staff_data()
    {
        $company_id = $this->input->post('company_id');
        $staff_list = $this->staff_model->get_staff($company_id);

        echo json_encode($staff_list);
    }

    public function instruction_save()
    {
        $mode = $this->input->post('mode');
        $patient_id = $this->input->post('patientId');
        $company_id = $this->input->post('company');
        $staff_id = $this->input->post('staff');
        $instruction_start = $this->input->post('patient_usefrom');
        $instruction_end = $this->input->post('patient_useto');

        $check_company_id = $this->company_model->getFromId($company_id);
        $check_staff_id = $this->staff_model->getFromId($staff_id);

        if(!empty($check_company_id) && !empty($check_staff_id)) {
            $data = array(
                'staff_id' => $staff_id,
                'patient_id' => $patient_id,
                'instruction_start' => $instruction_start,
                'instruction_end' => $instruction_end
            );
            
            if ($mode == 'update') {
                $id = $this->input->post('instruction_id');
                $this->instruction_model->instruction_update($id, $data);
            } else if ($mode == 'save') {
                $this->instruction_model->instruction_add($data);
            }
            
            redirect('instruction');
        } else {
            $this->session->set_flashdata('error', '会社情報またはスタッフ情報が不足しています。');
            redirect('instruction/add');
        }
    }

    public function print()
    {
        $instructionId = $this->input->post('instructionId');

        $data = $this->instruction_model->get_instruction_by_id($instructionId);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Noto Sans JP')->setSize(16); 
        $sheet->getStyle('A1:A11')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('E1:E11')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A1:E1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A11:E11')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        
        $sheet->setCellValue('B2', $data['company_name'] . "病院");
        $sheet->setCellValue('B3', $data['staff_name'] . "先生");
        $sheet->setCellValue('D4', $data['company_name'] . "事業所");
        $sheet->setCellValue('D5', "担当者");
        $sheet->setCellValue('C7', "指示依頼");
        $sheet->setCellValue('C8', $data['patient_name'] . "さんの指示をお願いします。");
        $sheet->setCellValue('C9', "指示期間：" . $data['instruction_start'] . "～" . $data['instruction_end']);

        $sheet->getRowDimension(1)->setRowHeight(42);
        $sheet->getRowDimension(2)->setRowHeight(26);
        $sheet->getRowDimension(3)->setRowHeight(26);
        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->getRowDimension(5)->setRowHeight(26);
        $sheet->getRowDimension(6)->setRowHeight(26);
        $sheet->getRowDimension(7)->setRowHeight(26);
        $sheet->getRowDimension(8)->setRowHeight(35);
        $sheet->getRowDimension(9)->setRowHeight(26);
        $sheet->getRowDimension(10)->setRowHeight(26);

        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(11);
        $sheet->getColumnDimension('E')->setWidth(7);

        $filename = $data['company_name']."_".$data['staff_name']."_". $instructionId . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
        
        redirect('instruction');
    }
}

?>
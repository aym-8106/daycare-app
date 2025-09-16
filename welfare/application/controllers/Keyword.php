<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/UserController.php';

/**
 * Class : キーワード
 *
 */
class keyword extends UserController
{
    public $categoryList = [];
    public $categoryIDList = [];
    public $labelList = [];
    public $userList = [];

    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        $this->load->model('keyword_model');
        $this->load->model('company_model');
        $this->load->model('setting_model');

        //キーワード
        $this->data['page'] = 'keyword';
        $this->header['title'] = 'キーワード一覧';

        //Init Setting
        $this->data['company'] = $this->company_model->get($this->user['company_id']);
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        $this->data['id'] = $this->input->post('id');
        $this->data['keyword'] = $this->input->post('keyword');

        if ($mode == 'register') {

            $this->form_validation->set_rules('id', 'ID', 'trim|integer');
            $this->form_validation->set_rules('keyword', 'キーワード', 'trim|required|max_length[255]');

            if ($this->form_validation->run() === TRUE) {
                if ($this->keyword_model->isExist($this->data['keyword'], $this->data['id'], $this->user['company_id'])) {
                    $this->session->set_flashdata('error', '既に存在するキーワードです。');
                } else {
                    if ($this->data['id']) {
                        $keyword = [
                            'id' => $this->data['id'],
                            'company_id' => $this->user['company_id'],
                            'keyword' => $this->data['keyword'],
                        ];
                        $this->keyword_model->register($keyword, 'id');
                        $this->session->set_flashdata('success', 'キーワードを更新しました。');
                    } else {
                        $keyword = [
                            'keyword' => $this->data['keyword'],
                            'company_id' => $this->user['company_id'],
                            'create_date' => date('Y-m-d H:i:s'),
                        ];
                        $this->keyword_model->add($keyword);
                        $this->session->set_flashdata('success', 'キーワードを追加しました。');
                    }
                }
            }
        } elseif ($mode == 'delete') {
            if ($this->data['id']) {
                $this->keyword_model->delete_force($this->data['id'], 'id');
            } else {
                $this->session->set_flashdata('error', 'エラーが発生しました。');
            }
        }
        $this->data['id'] = '';
        $this->data['keyword'] = '';
        $search = [
            'company_id' => $this->user['company_id']
        ];

        $this->data['list'] = $this->keyword_model->getList("", $search);

        $this->_load_view("keyword/index");
    }

    public function test()
    {
        $search = [
            'company_id' => $this->user['company_id']
        ];
        $this->data['company'] = $this->company_model->get($this->user['company_id'], 'company_id');
        $this->data['list'] = $this->keyword_model->getList("keyword", $search);
        foreach ($this->data['list'] as &$item) {
            $item->keyword2 = base64_encode($item->keyword);
        }
        $this->_load_view("keyword/test");
    }

    public function js($uuid)
    {
        $company = $this->company_model->getFromUUID($uuid);
        if (empty($company) || empty($company['bot_flag'])) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        header("Content-Type: application/javascript; charset=utf-8");
        header("Cache-Control: max-age=604800, public");
        $this->load->library('parser');

        $data = array(
            'list' => $this->keyword_model->getList("keyword", ''),
        );

        $id = 0;
        $list = $this->_scenario($id, $company['company_id'], $company['msg1']);

        $this->parser->parse('keyword/js', $data);
    }
}

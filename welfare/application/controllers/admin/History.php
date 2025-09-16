<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class History extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->load->model('user_model');

        $this->header['page'] = 'company';
        $this->header['title'] = 'ログイン履歴';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index($company_id = NULL)
    {
        $company_id = ($company_id == NULL ? 0 : $company_id);

        $this->data["company_id"] = $company_id;
        $this->data["company"] = $this->company_model->getFromId($company_id);

        $this->data['search'] = array(
            'company_id' => $company_id,
            'searchText' => $this->input->post('searchText'),
            'fromDate' => $this->input->post('fromDate'),
            'toDate' => $this->input->post('toDate'),
        );

        $this->load->library('pagination');

        $this->data['list_cnt'] = $this->company_model->loginHistory($this->data['search'], true);

        $returns = $this->_paginationCompress("admin/history/index/" . $company_id . "/", $this->data['list_cnt'], 10, 5);

        $this->data['list'] = $this->company_model->loginHistory($this->data['search'], false, $returns["page"], $returns["segment"]);

        $this->_load_view_admin("admin/history/index");
    }

    /**
     * @param null $company_id
     */
    function loginHistoy($company_id = NULL)
    {
        $company_id = ($company_id == NULL ? 0 : $company_id);

        $this->data["company_id"] = $company_id;
        $this->data["company"] = $this->company_model->getFromId($company_id);

        $this->data['search'] = array(
            'company_id' => $company_id,
            'searchText' => $this->input->post('searchText'),
            'fromDate' => $this->input->post('fromDate'),
            'toDate' => $this->input->post('toDate'),
        );

        $this->load->library('pagination');

        $this->data['list_cnt'] = $this->company_model->loginHistory($this->data['search'], true);

        $returns = $this->_paginationCompress("admin/history/index/" . $company_id . "/", $this->data['list_cnt'], 10, 5);

        $this->data['list'] = $this->company_model->loginHistory($this->data['search'], false, $returns["page"], $returns["segment"]);

        $this->_load_view_admin("admin/history/index");
    }

}
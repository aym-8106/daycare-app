<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/AdminController.php';

class Dashboard extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->header['page'] = 'dashboard';
        $this->header['title'] = '管理画面|ダッシュボード';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->load->model('company_model');
        $this->load->model('staff_model');

        $this->data['company_count'] = $this->company_model->getListCount(array('del_flag' => 0));
        $this->data['staff_count'] = $this->staff_model->getListCount(array('del_flag' => 0));

        $this->_load_view_admin("/admin/dashboard");
    }

}

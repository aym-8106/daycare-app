<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/WixController.php';

/**
 * Class AdminController
 */
class AdminController extends WixController
{
    public $data;
    public $header;
    public $footer;
    public $user;

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct($role = ROLE_GUEST)
    {
        parent::__construct();

        $this->session->set_flashdata('success', '');
        $this->session->set_flashdata('error', '');

        $this->header['page'] = $this->uri->segment(1);
        $this->header['role'] = $role;
        if (!$this->_login_check($role)) {
            if ($role == ROLE_ADMIN) redirect('/admin/login');
            else if ($role == ROLE_STAFF) redirect('/login');
            else if ($role == ROLE_COMPANY) redirect('/company/login');
        } else {
            if ($role == ROLE_ADMIN) $this->header['title'] = '管理画面【管理者用】';
            else if ($role == ROLE_STAFF) $this->header['title'] = '管理画面【企業用】';
            else if ($role == ROLE_COMPANY) $this->header['title'] = '管理画面【企業管理者用】';

        }
    }

    function logout($role = ROLE_ADMIN)
    {
        switch ($role) {
            case ROLE_ADMIN:
                $this->session->unset_userdata('admin');
                redirect('admin/login');
                break;
            case ROLE_STAFF:
                $this->session->unset_userdata('staff');
                redirect('login');
                break;
            case ROLE_COMPANY:
                $this->session->unset_userdata('company');
                redirect('login');
                break;
        }

        $this->session->sess_destroy();
    }

    function _login_check($role = ROLE_GUEST)
    {
        if ($role == ROLE_GUEST) return true;
        switch ($role) {
            case ROLE_ADMIN:
                $this->user = $this->session->userdata('admin');
                if (!empty($this->user)) {
                    $this->header['user'] = $this->user;
                    return true;
                }
                break;
            case ROLE_COMPANY:
                $company = $this->session->userdata('company');
                if (!empty($company['company_id'])) {
                    $this->user = $this->company_model->get($company['company_id']);
                    $this->header['user'] = $this->user;
                    return true;
                }
                break;
        }

        return false;
    }

    function _load_view_only($viewName = "")
    {
        $this->load->view($viewName, $this->data);
    }

    function _load_view($viewName = "", $prefix = '')
    {
        $this->load->view($prefix . 'includes/header', $this->header);
        $this->load->view($viewName, $this->data);
        $this->load->view($prefix . 'includes/footer', $this->footer);
    }

    function _load_view_admin($viewName = "")
    {
        $this->load->view('admin/includes/header', $this->header);
        $this->load->view($viewName, $this->data);
        $this->load->view('admin/includes/footer', $this->footer);
    }

    function _search_url($text)
    {
        $index = strpos($text, 'http://');
        if ($index !== FALSE) {
            $prefix = substr($text, 0, $index);
            $real_url = substr($text, $index);
            $ref_url = filter_var($real_url, FILTER_SANITIZE_URL);
            $href_url = str_replace($ref_url, ('<a href="' . $ref_url . '">' . $ref_url . '</a>'), $real_url);
            return $prefix . " " . $href_url;
        } else {
            $index = strpos($text, 'https://');
            if ($index !== FALSE) {
                $prefix = substr($text, 0, $index);
                $real_url = substr($text, $index);
                $ref_url = filter_var($real_url, FILTER_SANITIZE_URL);
                $href_url = str_replace($ref_url, ('<a href="' . $ref_url . '">' . $ref_url . '</a>'), $real_url);
                return $prefix . " " . $href_url;
            }
        }
        return $text;
    }

    /**
     * This function used provide the pagination resources
     * @param {string} $link : This is page link
     * @param {number} $count : This is page count
     * @param {number} $perPage : This is records per page limit
     * @return {mixed} $result : This is array of records and pagination data
     */
    function _paginationCompress($link, $count, $perPage = 10, $segment = SEGMENT)
    {
        $this->load->library('pagination');

        $config ['base_url'] = base_url() . $link;
        $config ['total_rows'] = $count;
        $config ['uri_segment'] = $segment;
        $config ['per_page'] = $perPage;
        $config ['num_links'] = 5;
        $config ['full_tag_open'] = '<nav><ul class="pagination">';
        $config ['full_tag_close'] = '</ul></nav>';
        $config ['first_tag_open'] = '<li class="arrow">';
        $config ['first_tag_close'] = '</li>';
        $config ['prev_tag_open'] = '<li class="arrow">';
        $config ['prev_tag_close'] = '</li>';
        $config ['next_tag_open'] = '<li class="arrow">';
        $config ['next_tag_close'] = '</li>';
        $config ['cur_tag_open'] = '<li class="active"><a href="#">';
        $config ['cur_tag_close'] = '</a></li>';
        $config ['num_tag_open'] = '<li>';
        $config ['num_tag_close'] = '</li>';
        $config ['last_tag_open'] = '<li class="arrow">';
        $config ['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $page = $config ['per_page'];
        $segment = $this->uri->segment($segment);

        return array(
            "page" => $page,
            "segment" => $segment
        );
    }

    function _paginationCompress2($link, $count, $per_page = 10)
    {
        $this->load->library('pagination');
        $cur_page = $this->input->post('page');

        $config ['base_url'] = $link;
        $config ['first_url'] = $link . '()';
        $config ['prefix'] = '(';
        $config ['suffix'] = ')';
        $config ['total_rows'] = $count;
        $config ['cur_page'] = $cur_page;
        $config ['per_page'] = $per_page;
        $config ['num_links'] = 5;
        $config ['full_tag_open'] = '<nav><ul class="pagination">';
        $config ['full_tag_close'] = '</ul></nav>';
        $config ['first_tag_open'] = '<li class="arrow">';
        $config ['first_tag_close'] = '</li>';
        $config ['prev_tag_open'] = '<li class="arrow">';
        $config ['prev_tag_close'] = '</li>';
        $config ['next_tag_open'] = '<li class="arrow">';
        $config ['next_tag_close'] = '</li>';
        $config ['cur_tag_open'] = '<li class="active"><a href="#">';
        $config ['cur_tag_close'] = '</a></li>';
        $config ['num_tag_open'] = '<li>';
        $config ['num_tag_close'] = '</li>';
        $config ['last_tag_open'] = '<li class="arrow">';
        $config ['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $page = $config ['per_page'];
        $segment = $cur_page;

        return array(
            "page" => $page,
            "segment" => $segment
        );
    }
}

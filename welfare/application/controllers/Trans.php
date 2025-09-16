<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/AdminController.php';

class Trans extends UserController
{

    public function index()
    {
        $this->load->view('trans/index');
    }

}

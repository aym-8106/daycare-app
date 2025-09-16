<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/UserController.php';

class Logout extends UserController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {

        $this->logout();
    }

}
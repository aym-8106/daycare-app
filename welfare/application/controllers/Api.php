<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'core/ApiController.php';

/*
 *  Api
 */

class Api extends ApiController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('company_model');

        if ($this->input->server('REQUEST_METHOD') === 'OPTIONS') {
            echo(json_encode(array('result' => 'fail')));
            exit;
        }
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        echo(json_encode([]));
    }

}


<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/UserController.php';

class Upload extends UserController
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
        header('Content-Type: application/json');

        if (file_exists("images/" . $_FILES["upload"]["name"])) {
            $result = array(
                'uploaded' => 0,
                'fileName' => '',
                'url' => base_url() . 'assets/upload/',
                'error' => $_FILES["upload"]["name"] . " already exists. ",
            );

            echo json_encode($result);
        } else {
            move_uploaded_file($_FILES["upload"]["tmp_name"],
                UPLOAD_PATH . "" . $_FILES["upload"]["name"]);
            $result = array(
                'uploaded' => 1,
                'fileName' => $_FILES["upload"]["name"],
                'url' => base_url() . 'assets/upload/' . $_FILES["upload"]["name"],
            );

            echo json_encode($result);
        }
    }

}
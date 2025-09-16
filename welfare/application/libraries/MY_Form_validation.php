<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Form Validation Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/pagination.html
 */
class MY_Form_validation extends CI_Form_validation {

    /**
     * Match one field to another
     *
     * @param	string	$date	string to compare against
     * @param	string	$field
     * @return	bool
     */
    public function past_date($date, $field)
    {
        $res= FALSE;
        if(isset($this->_field_data[$field], $this->_field_data[$field]['postdata'])){
            $date1 = strtotime($this->_field_data[$field]['postdata']);
            $date2 = strtotime($date);
            $res = ($date2>$date1)? TRUE:FALSE;
        }
        return $res;
    }

}

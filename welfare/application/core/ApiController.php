<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/WixController.php';
/**
 * Class ApiController
 */
class ApiController extends WixController
{
    public $data;
    public $user;
    public $user_id;

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct()
    {
        parent::__construct();

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('Content-Type: application/json');
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
        //return preg_replace('!(http://[a-z0-9_./?=&-]+)!i', '<a href="$1">$1</a> ', $text." ");

        return $text;
    }

    /*******************************************************************
     * Microsoft Translate APIs
     ******************************************************************/
    function com_create_guid2() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    protected function ms_translate($key,$text,$english = false)
    {
//        $key = '340ee97fc0a44cd8b5c739ecd939eaf8';
//        $key = '4ef1dcef5e474e5d9c5e31c750d85d22';

        $url = 'https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&from=en&to=ja';

        if($english){
            $url = 'https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&to=en';
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $post_data = array (
            array (
                'Text' => $text,
            ),
        );
        $content=json_encode($post_data);
        $headers = array(
            "Content-Type: application/json",
            "Ocp-Apim-Subscription-Region: japaneast",
            "Ocp-Apim-Subscription-Key: " . $key,
            "X-ClientTraceId: " . $this->com_create_guid2(),
            "Content-length: " . strlen($content) ,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        if($resp === false)
        {
            return $text;
        }

        curl_close($curl);
        $result = (array)json_decode($resp);

        if(isset($result[0]->translations)){
            return $result[0]->translations;
        }
        return $text;

    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class WixController
 */
class WixController extends CI_Controller
{
    public $wix_local;
    public $wix_api_domain;
    public $wix_api_key;
    public $wix_api_secret;
    public $wix_brand;
    public $wix_token;
    public $err_code;

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct()
    {
        parent::__construct();
        $this->wix_local = 'ja';
    }

    protected function debug($val, $exit = true)
    {
        echo '<pre/>';
        print_r($val);
        if ($exit) die;
    }

    protected function wix_valid()
    {
        if (empty($this->wix_api_domain) || empty($this->wix_api_key) || empty($this->wix_api_secret)) return false;
        return true;
    }

    protected function wix_set_param($domain, $key, $secret, $wix_brand = '', $brand_key = 'brand', $domain_key = 'company_wix_domain', $local = 'ja')
    {
        $this->wix_token = '';
        $this->wix_local = $local;
        $this->wix_api_domain = $domain;
        $this->wix_api_key = $key;
        $this->wix_api_secret = $secret;
        $this->wix_brand = $wix_brand;
        if (empty($this->wix_brand)) {
            $this->wix_brand = $this->wix_get_brand();
            $this->company_model->update([$domain_key => $domain, $brand_key => $this->wix_brand], $domain_key);
        }
    }

    protected function wix_set_param2($domain, $key, $secret, $wix_brand = '', $brand_key = 'brand', $local = 'ja')
    {
        $this->wix_token = '';
        $this->wix_local = $local;
        $this->wix_api_domain = $domain;
        $this->wix_api_key = $key;
        $this->wix_api_secret = $secret;
        $this->wix_brand = $wix_brand;
    }

    protected function wix_get_url($path = '')
    {
        if($this->wix_api_domain == 'baystars-tci-baystars'){
            return 'https://faq.baystars.co.jp/api/v1/' . $path;
        }
        return 'https://' . $this->wix_api_domain . '.wixanswers.com/api/v1/' . $path;
    }

    protected function wix_get_token($userId = '')
    {
        if (!$this->wix_valid()) return false;

        if (!empty($this->wix_token)) {
            return $this->wix_token;
        }
        $path = 'accounts/token';
        $method = 'POST';
        $payload = array(
            'keyId' => $this->wix_api_key,
            'secret' => $this->wix_api_secret,
        );
        if (!empty($userId)) {
            $payload['userId'] = $userId;
        }
        if (!empty($this->wix_brand)) {
            $payload['brandId'] = $this->wix_brand;
        }
        $result = $this->wix_call_api($path, $method, $payload, false);

        return !empty($result['token']) ? $result['token'] : false;
    }

    protected function wix_get_brand2()
    {
        if (!$this->wix_valid()) return false;

        $path = 'brand';
        $method = 'GET';
        $payload = [];
        $brandList = $this->wix_call_api($path, $method, $payload, true);
		if (empty($brandList)) {
			return '';
		}
        foreach ($brandList as $item) {
            if ($item->name == $this->wix_api_domain) {
                return $item->id;
            }
            if (!empty($item->settings->defaultHostName) && strtolower($item->settings->defaultHostName) == strtolower($this->wix_api_domain)) {
                return $item->id;
            }
            if (!empty($item->settings->helpCenter->domain->name) && $item->settings->helpCenter->domain->name == $this->wix_api_domain . '.wixanswers.com') {
                return $item->id;
            }
        }
        return '';
    }

    protected function wix_get_brand()
    {
        $brand = $this->wix_get_category_brand();
        if ($brand) {
            return $brand;
        } else {
            $brand = $this->wix_get_article_brand();
            if ($brand) {
                return $brand;
            }
        }
        return $this->wix_get_brand2();
    }

    protected function wix_call_api($path, $method, $payload, $is_auth = true, $userId = '')
    {
        $this->err_code = '';
        if ($is_auth == true && empty($this->wix_token)) {
            $this->wix_token = $this->wix_get_token($userId);
            if (empty($this->wix_token)) return '';
        }

        $api_url = $this->wix_get_url($path);
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case 'GET':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if (!empty($payload)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($payload)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($payload)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                }
                break;
            default:
                break;
        }

        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json; charset=utf-8",
            "X-Requested-With: XMLHttpRequest",
        );

        if ($is_auth) {
            $headers[] = "Authorization: Bearer " . $this->wix_token;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        $resp = curl_exec($ch);
        if (!$resp) {
            $info = curl_getinfo($ch);
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $this->err_code = $resultStatus;
            //if ($this->err_code == 404 || $this->err_code == 403)
            {
                //log_message('error', '404 error with wix api :' . $api_url . ' method:' . $method . ' payload:' . json_encode($payload));
                //if(!empty($_SERVER['argv'])){
                //    log_message('error', 'server:' .  json_encode($_SERVER['argv']) );
                //}
                //log_message('error', 'curl info:' . json_encode($info));
            }

            /*if ($_SERVER['REMOTE_ADDR'] == '188.43.136.34' || $_SERVER['REMOTE_ADDR'] == '::1') {
                $this->debug($api_url, false);
                $this->debug($resultStatus, false);
            }*/
            return [];
        }
        curl_close($ch);
        return (array)json_decode($resp);
    }

    /***************************************************************************************************
     * Analysis APIs
     **************************************************************************************************/
    protected function wix_analytics($start_date, $end_date, $pageSize = 200)
    {
        $path = 'analytics/article/mostRead';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'startDate' => $start_date,
            'endDate' => $end_date,
            'histogramInterval' => 2,
            'pageSize' => $pageSize,
        );
        //categoryIds
        //sourceId
        //sourceType

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_analytics_brand()
    {
        $path = 'analytics';
        $method = 'POST';
        $payload = array(
            'locale' => 'ja',
            'sourceId' => null,
            'sourceType' => 0,
            'type' => 10,
        );

        return $this->wix_call_api($path, $method, $payload, true);
    }

    /***************************************************************************************************
     * Categories APIs
     **************************************************************************************************/

    protected function wix_get_category_brand()
    {
        $path = 'categories?locale=ja';
        $method = 'GET';
        $payload = [];
        $result = $this->wix_call_api($path, $method, $payload, true);
        $brand = '';
        if (!empty($result[0])) {
            $brand = $result[0]->brandId;
        }
        return $brand;
    }

    protected function wix_get_categories()
    {
        $path = 'categories?locale=ja';
        $method = 'GET';
        $payload = [];
        $result = $this->wix_call_api($path, $method, $payload, true);
        return $result;
    }

    protected function wix_get_category_list()
    {
        $path = 'categories/admin?locale=ja';
        if ($this->wix_brand) $path .= '&brandId=' . $this->wix_brand;
        $method = 'GET';
        $payload = [];
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_add_category($categoryName, $parent_id = '')
    {
        $path = 'categories';
        if ($parent_id) {
            $path = 'categories/' . $parent_id;
        }
        $method = 'POST';
        $payload = array(
            'name' => $categoryName,
        );
        if ($this->wix_brand) $payload['brandId'] = $this->wix_brand;
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_delete_category($category_id)
    {
        if (empty($category_id)) return false;
        $path = 'categories/' . $category_id;
        $method = 'DELETE';
        $payload = [
            'locale' => $this->wix_local,
        ];
        return $this->wix_call_api($path, $method, $payload, true);
    }


    /***************************************************************************************************
     * Articles APIs
     **************************************************************************************************/

    protected function wix_get_article_brand()
    {
        $path = 'articles/search';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'text' => "",
            'spellcheck' => true,
            'pageSize' => 1,
            'page' => 1,
            'statuses' => [10],
        );

        $result = $this->wix_call_api($path, $method, $payload, true);
        $brand = '';

        if (!empty($result['items'][0])) {
            $brand = $result['items'][0]->brandId;
        }
        return $brand;
    }

    protected function wix_get_article_public()
    {
        $path = 'articles/search';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'text' => "",
            'spellcheck' => true,
            'pageSize' => 1,
            'page' => 1,
            'statuses' => [10],
        );

        $result = $this->wix_call_api($path, $method, $payload, true);

        return $result;
    }

    protected function wix_add_note($article_id, $content)
    {
        $path = 'articles/' . $article_id . '/notes';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'content' => $content,
        );

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_note($article_id)
    {
        $path = 'articles/' . $article_id . '/notes' . "?locale=" . $this->wix_local;
        $method = 'GET';
        $payload = [];

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_delete_note($article_id, $note_id)
    {
        $path = 'articles/' . $article_id . '/notes/' . $note_id . "?locale=" . $this->wix_local;
        $method = 'DELETE';
        $payload = [];

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_label_list()
    {
        $path = 'labels';
        $method = 'GET';
        $payload = [];

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_add_label($labelName)
    {
        $path = 'labels';
        $method = 'POST';
        $payload = array(
            'name' => $labelName,
        );

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_add_article_keyword($article_id, $keywords)
    {
        $path = 'articles/' . $article_id . '/phrases';
        $method = 'PUT';
        $payload = array(
            'locale' => $this->wix_local,
            'textValues' => explode(",", $keywords),
        );

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_add_article_label($article_id, $labelIDs, $old_label_ids = [])
    {
        $path = 'articles/' . $article_id . '/labels';
        $method = 'PUT';
        $payload = array(
            'locale' => $this->wix_local,
            'addedLabelIds' => $labelIDs,
            'removedLabelIds' => $old_label_ids,
        );
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_add_article($category_id, $title, $contents, $status = 10, $type = 100)
    {
        if (empty($category_id) || empty($title) || empty($contents)) return false;

        if (substr($contents, 0, 4) != '<div') {
            $contents = '<div>' . $contents . '</div>';
        }
        $path = 'articles';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'categoryId' => $category_id,
            'type' => $type,        //100:Article 110:Feature Request 120:Known Issue 130:Video
            'status' => $status,    //0:非公開  10:公開 30:削除
            'title' => $title,
            'content' => $contents,
        );
        if ($this->wix_brand) $payload['brandId'] = $this->wix_brand;
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_update_article($article_id, $title, $contents)
    {
        if (empty($article_id) || empty($title) || empty($contents)) return false;

        if (substr($contents, 0, 4) != '<div') {
            $contents = '<div>' . $contents . '</div>';
        }

        $path = 'articles/' . $article_id;
        $method = 'PUT';
        $payload = array(
            'locale' => $this->wix_local,
            'title' => $title,
            'content' => $contents,
        );
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_publish_article($article_id, $title, $contents)
    {
        if (empty($article_id) || empty($title) || empty($contents)) return false;
        if (substr($contents, 0, 4) != '<div') {
            $contents = '<div>' . $contents . '</div>';
        }
        $path = 'articles/' . $article_id . '/publish';
        $method = 'POST';
        $payload = array(
            'id' => $article_id,
            'notify' => false,
            //'updateUrl' => false,
            'locale' => $this->wix_local,
            'title' => $title,
            'content' => $contents,
        );
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_unpublish_article($article_id)
    {
        if (empty($article_id)) return false;
        $path = 'articles/' . $article_id . '/unpublish';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
        );
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_update_article_category($article_id, $category_id)
    {
        if (empty($article_id)) return false;
        $path = 'articles/' . $article_id . '/move';
        $method = 'POST';
        $payload = array(
            'newCategoryId' => $category_id,
            'categoryId' => $category_id,
        );
        if ($this->wix_brand) $payload['brandId'] = $this->wix_brand;
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_article($article_id, $user_id = '')
    {
        if (empty($article_id)) return false;
        $path = 'articles/' . $article_id . "?locale=" . $this->wix_local;
        $method = 'GET';
        $payload = array(
            'locale' => $this->wix_local,
        );

        $result = $this->wix_call_api($path, $method, $payload, true, $user_id);
        if (!empty($result['brandId']) && $result['brandId'] == $this->wix_brand) {
            return $result;
        }
    }

    protected function wix_get_article_extend($article_id, $user_id = '')
    {
        if (empty($article_id)) return false;
        $path = 'articles/' . $article_id . "/extended?locale=" . $this->wix_local;
        $method = 'GET';
        $payload = array(
            'locale' => $this->wix_local,
        );

        $result = $this->wix_call_api($path, $method, $payload, true, $user_id);
        if (!empty($result['brandId']) && $result['brandId'] == $this->wix_brand) {
            return $result;
        }
    }

    protected function wix_search_article($search_text = '', $page_count = 5, $statuses = [10], $page = 1)
    {
        $path = 'articles/search/admin';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'text' => $search_text,
            'spellcheck' => true,
            'statuses' => $statuses,//10:公開
            'page' => $page,
            'pageSize' => $page_count,
        );
        if ($this->wix_brand) {
            $payload['brandId'] = $this->wix_brand;
        }

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_search_article_time($search_labels = [], $page = 1, $page_count = 5, $statuses = [10], $date_from = '', $date_to = '')
    {
        $path = 'articles/search/admin';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'hasAllOfLabelIds' => $search_labels,
            'spellcheck' => true,
            'statuses' => $statuses,//10:公開
            'page' => $page,
            'pageSize' => $page_count,
        );
        if ($this->wix_brand) {
            $payload['brandId'] = $this->wix_brand;
        }

        if ($date_from != '') {
            //fromCreationDate fromLastUpdateDate
            $payload['fromLastUpdateDate'] = date("Y-m-d\TH:i:s", strtotime($date_from) - 9 * 3600);
        }
        if ($date_to != '') {
            //toCreationDate toLastUpdateDate
            $payload['toLastUpdateDate'] = date("Y-m-d\TH:i:s", strtotime($date_to) - 9 * 3600);
        }
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_search_article_exact($search_text = '', $statuses = [0, 10])
    {
        $path = 'articles/search/admin';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'mustMatchText' => $search_text,
            'spellcheck' => true,
            'statuses' => $statuses,//10:公開
            'page' => 1,
            'pageSize' => 100,
        );
        if ($this->wix_brand) {
            $payload['brandId'] = $this->wix_brand;
        }

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_article_list($search_text = '', $page = 1, $page_count = 10, $statues = [0, 10], $sortType = 100)
    {
        $path = 'articles/search/admin';
        $method = 'POST';
        $payload = array(
            'locale' => $this->wix_local,
            'text' => $search_text,
            'spellcheck' => true,
            'statuses' => $statues,//0:draft 10:Published
            'page' => $page,
            'pageSize' => $page_count,
            'sortType' => $sortType,//Best match (text search) (100)
        );
        if ($this->wix_brand) {
            $payload['brandId'] = $this->wix_brand;
        }

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_delete_article($article_id)
    {
        if (empty($article_id)) return false;
        $path = 'articles/' . $article_id . "?locale=" . $this->wix_local;
        $method = 'DELETE';
        $payload = array(
            'locale' => $this->wix_local,
        );
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_savedreply($search_text, $page = 1, $pageSize = 10)
    {
        $path = "savedReplies/search";
        $method = 'POST';
        $payload = [
            'locale' => $this->wix_local,
            'text' => $search_text,
            'spellcheck' => true,
            'page' => $page,
            'pageSize' => $pageSize
        ];
        $result = $this->wix_call_api($path, $method, $payload, true);
        if (!empty($result['items'])) {
            foreach ($result['items'] as $item) {
                $item = (array)$item;
                if ($item['title'] == $search_text) {
                    return $item['content'];
                }
            }
        }
        return '';
    }


    /***************************************************************************************************
     * Ticket API
     **************************************************************************************************/

    /**
     * @param $search_text
     * @param int $page
     * @param int $page_count
     * @param string $date_from
     * @param string $date_to
     * @return array|string
     */
    protected function wix_get_ticket_list($search_text, $page = 1, $page_count = 10, $date_from = '', $date_to = '', $statuses = [100, 110, 120, 140, 150])
    {
        //Open: 100
        //Pending: 110
        //Closed: 120
        //Solved: 140
        //Investigating: 150
        $path = 'tickets/search/admin';
        $method = 'POST';
        $payload = array(
            'locales' => [$this->wix_local, 'en'],
//            'resultType' => 2,
            'text' => $search_text,
            'page' => $page,
            'pageSize' => $page_count,
            'filters' => array(
//                'spam'=> false,
//                'timeZone'=> 'UTC',
//                'currentlyHandled'=> false,
                'statuses' => $statuses,
            ),
        );

        if (!empty($search_text)) {
            //$payload['text'] = $search_text;
        }
        if ($date_from != '') {
            //fromCreationDate fromLastUpdateDate
            $payload['filters']['fromLastUpdateDate'] = date("Y-m-d\TH:i:s", strtotime($date_from) - 9 * 3600);
        }
        if ($date_to != '') {
            //toCreationDate toLastUpdateDate
            $payload['filters']['toLastUpdateDate'] = date("Y-m-d\TH:i:s", strtotime($date_to) - 9 * 3600);
        }
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_ticket_list_channel($search_text, $page = 1, $page_count = 10, $date_from = '', $date_to = '', $statuses = [100, 110, 120, 140, 150])
    {
        $path = 'tickets/search/admin';
        $method = 'POST';
        $payload = array(
            'locales' => [$this->wix_local, 'en'],
            'text' => $search_text,
            'page' => $page,
            'pageSize' => $page_count,
            'filters' => array(
//                'channelFilters'=>[180=>[]] ,
//                'channelFilters'=>[ 180],
                'statuses' => $statuses,
            ),
        );

        if ($date_from != '') {
            //fromCreationDate fromLastUpdateDate
            $payload['filters']['fromCreationDate'] = date("Y-m-d\TH:i:s", strtotime($date_from) - 9 * 3600);
        }
        if ($date_to != '') {
            //toCreationDate toLastUpdateDate
            $payload['filters']['toCreationDate'] = date("Y-m-d\TH:i:s", strtotime($date_to) - 9 * 3600);
        }
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_add_ticket($subject, $content, $email="test@wixanswers.com")
    {
        $path = 'tickets/guest';
        $method = 'POST';
        $payload = [
            "userEmail"=>$email,
            'brandId'=>$this->wix_brand,
            'locale'=>$this->wix_local,
            'subject'=>$subject,
            'content'=>$content,
            "status"=>100,
            "channel"=>180,
        ];
        return $this->wix_call_api($path, $method, $payload, true);
    }
    protected function wix_add_ticket_behalf($subject, $content, $recipientEmail)
    {
        $path = 'tickets/onBehalf';
        $method = 'POST';
        $payload = [
            "recipientEmail"=>$recipientEmail,
            'brandId'=>$this->wix_brand,
            'locale'=>$this->wix_local,
            'subject'=>$subject,
            'content'=>$content,
            "status"=>100,
            "channel"=>180,
        ];//var_dump($payload);die;
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_ticket($ticket_id)
    {
        $path = 'tickets/' . $ticket_id . '/admin';
        $method = 'GET';
        $payload = [];
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_tickets($ticket_ids, $page_count = 10)
    {
        $path = 'tickets/search/admin';
        $method = 'POST';
        $payload = array(
            'locales' => [$this->wix_local, 'en'], //Wix Updated!!!
            "ticketIds" => $ticket_ids,
            'pageSize' => $page_count,
        );
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_ticket_timeline($ticket_id)
    {
        $path = "tickets/{$ticket_id}/timeline";
        $method = 'GET';
        $payload = [];
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_ticket_fields()
    {
        $path = "tickets/fields";
        $method = 'GET';
        $payload = [];
        return $this->wix_call_api($path, $method, $payload, false);
    }

    //Update a Ticket's Custom Fields
    protected function wix_update_ticket_field($ticket_id, $customFields)
    {
        $path = "tickets/{$ticket_id}/fields";
        $method = 'PUT';
        $payload = ['customFields'=>$customFields];
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_ticket_replies($ticket_id)
    {
        $path = "tickets/{$ticket_id}/replies/admin";
        $method = 'GET';
        $payload = [];
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_add_ticket_note($ticket_id, $content)
    {
        $path = "tickets/" . $ticket_id . "/agentInternalNote";
        $method = 'POST';
        $payload = ['content' => $content];
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_delete_ticket($ticket_id)
    {
        $path = "tickets/" . $ticket_id . "/delete";
        $method = 'POST';
        $payload = [ "banUser"=> false];
        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_user_ticket($userId, $page = 1, $page_count = 300, $statuses = [100, 110, 120, 140, 150])
    {
        $path = 'tickets/search/admin';
        $method = 'POST';
        $payload = array(
            'locales' => [$this->wix_local, 'en'],
            'text' => '',
            'page' => $page,
            'pageSize' => $page_count,
            'filters' => array(
                'userIds' => [$userId],
                //'statuses'=> $statuses,
            ),
        );

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_past_tickets($date_from, $date_to, $page = 1, $page_count = 300, $statuses = [100, 110, 120, 140, 150])
    {
        $path = 'tickets/search/admin';
        $method = 'POST';
        $payload = array(
            'locales' => [$this->wix_local, 'en'],
            'text' => '',
            'page' => $page,
            'pageSize' => $page_count,
            'filters' => array(
                'statuses'=> $statuses,
            ),
        );

        if ($date_from != '') {
            //fromCreationDate fromLastUpdateDate
            $payload['filters']['fromCreationDate'] = date("Y-m-d\TH:i:s", strtotime($date_from) - 9 * 3600);
        }
        if ($date_to != '') {
            //toCreationDate toLastUpdateDate
            $payload['filters']['toCreationDate'] = date("Y-m-d\TH:i:s", strtotime($date_to) - 9 * 3600);
        }


        return $this->wix_call_api($path, $method, $payload, true);
    }

    /***************************************************************************************************
     * Users APIs
     **************************************************************************************************/

    /**
     * @return array|string
     */
    protected function wix_get_user_list($pageSize = 250, $isAgent = true)
    {
        $path = 'users/search/admin';
        $payload = array(
            'text' => "",
            'pageSize' => $pageSize,
            'isAgent' => $isAgent,
            'userTypes' => [5],//authenticated:1 Unauthenticated:2  Pending Authentication:3 Pending Deletion:5 Deleted:99
        );
        $method = "POST";
        return $this->wix_call_api($path, $method, $payload, true);
    }

    /**
     * @param $userId
     * @return array|string
     */
    protected function wix_get_user($userId)
    {
        $path = 'users/' . $userId;
        $payload = [];
        $method = 'GET';

        return $this->wix_call_api($path, $method, $payload, true);
    }

    protected function wix_get_brand_list($user_id = '')
    {
        $path = 'brand?enable=true';
        $payload = [
            'locale' => $this->wix_local,
        ];
        $method = 'GET';
        return $this->wix_call_api($path, $method, $payload, true, $user_id);
    }


    /***************************************************************************************************
     * Call Center APIs
     **************************************************************************************************/
    /**
     * Get Call Center Lines
     *
     * @return array|string
     */
    protected function wix_get_callcenter_lines()
    {
        $path = 'callcenter/lines';
        $payload = [];
        $method = 'GET';

        return $this->wix_call_api($path, $method, $payload, true);
    }

    /**
     * Get Call Center Queues
     *
     * @return array|string
     */
    protected function wix_get_callcenter_queues()
    {
        $path = 'callcenter/queues';
        $payload = [];
        $method = 'GET';

        return $this->wix_call_api($path, $method, $payload, true);
    }

    /***************************************************************************************************
     * Companies API
     **************************************************************************************************/

    /**
     * Get List of Companies
     *
     * @return array|string
     */
    protected function wix_get_companies()
    {
        $path = 'companies';
        $payload = [];
        $method = 'GET';

        return $this->wix_call_api($path, $method, $payload, true);
    }

    /***************************************************************************************************
     * Groups API
     **************************************************************************************************/

    /**
     * Get Groups
     *
     * @return array|string
     */
    protected function wix_get_groups()
    {
        $path = 'groups';
        $payload = [];
        $method = 'GET';

        return $this->wix_call_api($path, $method, $payload, true);
    }
    /***************************************************************************************************
     * GDPR APIs
     **************************************************************************************************/

    /**
     * Get User's Personally Identifying Information
     *
     * @param $uuid
     * @return array|string
     */
    protected function wix_get_user_pii($uuid)
    {
        $path = 'gdpr/pii/' . $uuid;
        $payload = [];
        $method = 'GET';

        return $this->wix_call_api($path, $method, $payload, true);
    }

    /**
     * Get User's Personal Information (PI)
     *
     * @param $uuid
     * @return array|string
     */
    protected function wix_get_user_pi($uuid)
    {
        $path = 'gdpr/pi/' . $uuid;
        $payload = [];
        $method = 'GET';

        return $this->wix_call_api($path, $method, $payload, true);
    }

    /**
     * Delete User's Personally Identifying Information (PPI)
     *
     * @param $uuid
     * @return array|string
     */
    protected function wix_delete_user_pii($uuid)
    {
        $path = 'gdpr/delete/' . $uuid;
        $payload = [];
        $method = 'DELETE';

        return $this->wix_call_api($path, $method, $payload, true);
    }

    /**
     *
     *
     * @return array|string
     */
    protected function wix_get_customer_list($fromDate = '', $toDate = '', $page = 1, $pageSize = 250)
    {
        $path = 'users/dashboard/search';
        $payload = [
            'isAgent' => false,
            'page' => $page,
            'pageSize' => $pageSize,
            'userSortType' => 50,
            //'fromCreationDate'=>$fromDate,//fromLastUpdateDate
            //'toCreationDate'=>$toDate,//toLastUpdateDate
        ];
        if (!empty($fromDate)) {
            $payload['fromCreationDate'] = date("Y-m-d\TH:i:s", strtotime($fromDate) - 9 * 3600);
        }
        if (!empty($toDate)) {
            $payload['toCreationDate'] = date("Y-m-d\TH:i:s", strtotime($toDate) - 9 * 3600);
        }
        $method = 'POST';

        return $this->wix_call_api($path, $method, $payload, true);
    }
}

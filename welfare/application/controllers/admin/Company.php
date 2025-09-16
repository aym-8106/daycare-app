<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/AdminController.php';

class Company extends AdminController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_ADMIN);

        $this->load->model('setting_model');

        $this->header['page'] = 'company';
        $this->header['title'] = '企業管理';
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $mode = $this->input->post('mode');
        if ($mode == 'update') {
            $use_flag = $this->input->post('use_flag');
            $id = $this->input->post('company_id');
            $data = array(
                'use_flag' => $use_flag,
                'company_id' => $id,
            );
            $this->company_model->saveSetting($data);
        }
        $this->data['search'] = $this->input->post('searchText');

        $this->load->library('pagination');

        $this->data['list_cnt'] = $this->company_model->getList('*', $this->data['search'], true);
        $returns = $this->_paginationCompress("admin/company/index", $this->data['list_cnt'], 10, 4);

        $this->data['start_page'] = $returns["segment"] + 1;
        $this->data['end_page'] = $returns["segment"] + $returns["page"];
        if ($this->data['end_page'] > $this->data['list_cnt']) $this->data['end_page'] = $this->data['list_cnt'];
        if (!$this->data['start_page']) $this->data['start_page'] = 1;

        $this->data['list'] = $this->company_model->getList('*', $this->data['search'], false, $returns['page'], $returns['segment']);

        $this->_load_view_admin("admin/company/index");
    }

    public function edit($_id)
    {
        if ($_id == null) {
            redirect('admin/company');
        }

        $company = $this->company_model->getFromId($_id);
        if (empty($company)) {
            redirect('admin/company');
        }
        $mode = $this->input->post('mode');
        if ($mode == 'faq_sync') {
//            $cnt = $this->faq_sync($_id);
//            $this->session->set_flashdata('success', $cnt.'件の記事を同期しました。');
        }
        if ($mode == 'save') {
            $this->data['company'] = array(
                'company_id' => $_id,
                'company_name' => $this->input->post('company_name'),
                'company_email' => $this->input->post('company_email'),
                'company_password' => $this->input->post('company_password'),

                'payment_date' => $this->input->post('payment_date'),
            );

            $this->form_validation->set_rules('company_name', '企業名', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('company_email', 'メールアドレス', 'trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('company_password', 'パスワード', 'max_length[20]');
            $this->form_validation->set_rules('company_password_confirm', 'パスワード（確認）', 'trim|matches[company_password]|max_length[20]');

            if ($this->form_validation->run() === TRUE) {
                if ($this->_check_email($this->data['company']['company_email'], $_id)) {
                    $this->data['company']['update_date'] = date('Y-m-d H:i:s');
                    if (!empty($this->data['company']['company_password'])) {
                        $this->data['company']['company_password'] = sha1($this->data['company']['company_password']);
                    } else {
                        unset($this->data['company']['company_password']);
                    }

                    $result = $this->company_model->edit($this->data['company'], 'company_id');
                    if ($result) {
                        $this->session->set_flashdata('success', '正常に更新されました。');
                        $this->session->set_flashdata('error', '');
                    } else {
                        $this->session->set_flashdata('success', '');
                        $this->session->set_flashdata('error', '更新に失敗しました。');
                    }
                } else {
                    $this->session->set_flashdata('error', '既に登録されているメールアドレスです。');
                }
            } else {
            }

        } else {
            $this->data['company'] = $company;
        }

        $this->_load_view_admin("admin/company/edit");

    }

    public function faq_sync($_id)
    {
        $start_time = microtime(true);
        ini_set('max_execution_time', 0);

        $company = $this->company_model->getFromId($_id);
        $wix_api_domain = $company['company_wix_domain'];
        $wix_api_key = $company['company_wix_key'];
        $wix_api_secret = $company['company_wix_secret'];
        $wix_brand = $company['brand'];
        $wix_api_domain2 = $company['company_wix_domain2'];
        $wix_brand2 = $company['brand2'];

        if (empty($wix_api_domain) || empty($wix_api_domain2) || empty($wix_api_key) || empty($wix_api_secret)) {
            $res = array(
                'ok' => false,
                'cnt' => 0,
                'time' => (microtime(true) - $start_time),
            );

            return $this->output->set_output(json_encode($res));
        }

        if ($company['external_type'] == 0) {//メイン→サブ 公開
            $this->wix_set_param($wix_api_domain, $wix_api_key, $wix_api_secret, $wix_brand);
        } else {//サブ→メイン 下書き
            $this->wix_set_param($wix_api_domain2, $wix_api_key, $wix_api_secret, $wix_brand2, 'brand2', 'company_wix_domain2');
        }

        //get label
        $searchLabels = [];

        $categories1 = $this->wix_get_category_list();
        $categoryList1 = ['00000000-0000-0000-0000-000000000000' => ''];
        foreach ($categories1 as $category) {
            $categoryList1[$category->id] = ['name' => $category->name, 'parent' => '00000000-0000-0000-0000-000000000000'];
            if (!empty($category->children)) {
                foreach ($category->children as $children) {
                    $categoryList1[$children->id] = ['name' => $children->name, 'parent' => $category->id];
                }
            }
        }
        $page = 0;
        $pageCnt = 300;//max 500
        $itemsCount = 0;
        do {
            $page = $page + 1;
            if ($company['external_type'] == 0) {//メイン→サブ 公開
                $this->wix_set_param($wix_api_domain, $wix_api_key, $wix_api_secret, $wix_brand);
            } else {//サブ→メイン 下書き
                $this->wix_set_param($wix_api_domain2, $wix_api_key, $wix_api_secret, $wix_brand2, 'brand2', 'company_wix_domain2');
            }
            $list = $this->wix_search_article('', $pageCnt, [0, 10], $page);

            if (empty($list)) break;
            $numPages = $list['numPages'];
            //$itemsCount = $list['itemsCount'];
            if ($company['external_type'] == 0) {
                $method = 'メイン→サブ';
            } else {
                $method = 'サブ→メイン';
            }

            if (!empty($list['items'])) {

                //label2 category2 0-サブ
                if ($company['external_type'] == 0) {//メイン→サブ 公開
                    $this->wix_set_param($wix_api_domain2, $wix_api_key, $wix_api_secret, $wix_brand2, 'brand2', 'company_wix_domain2');
                } else {//サブ→メイン 下書き
                    $this->wix_set_param($wix_api_domain, $wix_api_key, $wix_api_secret, $wix_brand);
                }

                //get label
                $this->labelList2 = [];
                $labels = $this->wix_get_label_list();
                if (!empty($labels)) {
                    foreach ($labels as $item) {
                        $this->labelList2[$item->name] = (array)$item;
                    }
                }
                //get category
                $categories2 = $this->wix_get_category_list();
                $categoryList2 = [];
                foreach ($categories2 as $category) {
                    $categoryList2[$category->name] = ['id' => $category->id, 'parent' => ''];
                    if (!empty($category->children)) {
                        foreach ($category->children as $children) {
                            $categoryList2[$category->name . '||' . $children->name] = ['id' => $children->id, 'parent' => $category->name];
                            //$categoryList2[$children->name] = ['id' => $children->id, 'parent' => $category->name];
                        }
                    }
                }
                //register article
                foreach ($list['items'] as $article) {
                    if(empty($article->title)) continue;
                    $itemsCount++;

                    //noteの取得　0-メイン
                    if ($company['external_type'] == 0) {//メイン→サブ 公開
                        $this->wix_set_param($wix_api_domain, $wix_api_key, $wix_api_secret, $wix_brand);
                    } else {//サブ→メイン 下書き
                        $this->wix_set_param($wix_api_domain2, $wix_api_key, $wix_api_secret, $wix_brand2, 'brand2', 'company_wix_domain2');
                    }
                    $org_note = $this->wix_get_note($article->id);

                    //記事の登録 0-サブ
                    if ($company['external_type'] == 0) {
                        $this->wix_set_param($wix_api_domain2, $wix_api_key, $wix_api_secret, $wix_brand2, 'brand2', 'company_wix_domain2');
                    } else {
                        $this->wix_set_param($wix_api_domain, $wix_api_key, $wix_api_secret, $wix_brand);
                    }

                    $updTime = date('Y-m-d H:i:s', $article->lastUpdateDate / 1000);

                    $orgCat = $categoryList1[$article->categoryId];//name, parent_id
                    $cat_id = '00000000-0000-0000-0000-000000000000';
                    if (!empty($orgCat)) {
                        if ($orgCat['parent'] == '00000000-0000-0000-0000-000000000000') {
                            //main category
                            if (isset($categoryList2[$orgCat['name']]) && $categoryList2[$orgCat['name']]['parent'] == '') {
                                $cat_id = $categoryList2[$orgCat['name']]['id'];
                            } else {
                                //add main category
                                $new_category = $this->wix_add_category($orgCat['name']);
                                $cat_id = $new_category['id'];
                                $categoryList2[$orgCat['name']] = ['id' => $cat_id, 'parent' => ''];
                            }
                        } else {
                            //sub category
                            $orgParentCat = $categoryList1[$orgCat['parent']];//name , parent
                            if (isset($categoryList2[$orgParentCat['name']]) && $categoryList2[$orgParentCat['name']]['parent'] == '') {//parent category is registered
                                //exist main category
                                //print_r($categoryList2);
                                $mainCatId = $categoryList2[$orgParentCat['name']]['id'];
                                if (isset($categoryList2[$orgParentCat['name'] . '||' . $orgCat['name']]) && $categoryList2[$orgParentCat['name'] . '||' . $orgCat['name']]['parent'] == $orgParentCat['name']) {
                                    //exist sub category
                                    $cat_id = $categoryList2[$orgParentCat['name'] . '||' . $orgCat['name']]['id'];
                                } else {
                                    //add sub category
                                    $new_category = $this->wix_add_category($orgCat['name'], $mainCatId);
                                    $cat_id = $new_category['id'];
                                    $categoryList2[$orgParentCat['name'] . '||' . $orgCat['name']] = ['id' => $cat_id, 'parent' => $orgParentCat['name']];
                                }
                            } else {
                                //add main category
                                $new_main_category = $this->wix_add_category($orgParentCat['name']);
                                $categoryList2[$orgParentCat['name']] = ['id' => $new_main_category['id'], 'parent' => ''];
                                //add sub category
                                $new_category = $this->wix_add_category($orgCat['name'], $new_main_category['id']);
                                $cat_id = $new_category['id'];
                                $categoryList2[$orgParentCat['name'] . '||' . $orgCat['name']] = ['id' => $cat_id, 'parent' => $orgParentCat['name']];
                            }
                        }
                    }

                    //記事の存在を確認する
                    $exist_id = '';
                    $articleList = $this->wix_search_article_exact($article->title);
                    if (!empty($articleList['items'])) {
                        foreach ($articleList['items'] as $item) {
                            if ($item->title == $article->title) {
                                if (empty($exist_id)) {
                                    $exist_id = $item->id;
                                } else {
                                    $this->wix_delete_article($item->id);
                                }
                            }
                        }
                    }
                    $new_status = 0;
                    if ($company['external_type'] == 0) {
                        $new_status = $article->status;
                    }
                    if ($article->status != 30) {
                        if (empty($exist_id)) {
                            $new_article = $this->wix_add_article($cat_id, $article->title, $article->content, $new_status);
                        } else {
                            $new_article = $this->wix_update_article($exist_id, $article->title, $article->content);
                            if ($new_status != $new_article['status']) {
                                if ($new_status = 10) {//Published
                                    $this->wix_publish_article($exist_id, $article->title, $article->content);
                                } elseif ($new_status = 0) {//Draft
                                    $this->wix_unpublish_article($exist_id);
                                }
                            }
                            if ($cat_id != $new_article['categoryId']) {
                                $a = $this->wix_update_article_category($exist_id, $cat_id);
                            }
                        }
                        $new_id = $new_article['id'];

                        //ラベル
                        $label_ids = $this->_get_label_ids($article->labels);
                        if (!empty($label_ids) || !empty($old_label_ids)) {
                            $new_article = $this->wix_add_article_label($new_id, $label_ids, []);
                        }

                        //キーワード
                        $keywords = join(',', $article->phrases);
                        $new_article = $this->wix_add_article_keyword($new_id, $keywords);
                        //メモ
                        if (!empty($org_note)) {
                            $new_note = $this->wix_get_note($new_id);
                            foreach ($new_note as $note) {
                                foreach ($org_note as $key => $note2) {
                                    if ($note->content == $note2->content) {
                                        unset($org_note[$key]);
                                        //$this->wix_delete_note($new_id, $note->id);
                                        break;
                                    }
                                }
                            }
                            foreach ($org_note as $note) {
                                $this->wix_add_note($new_id, $note->content);
                            }
                        }

                    } else {
                        if (!empty($exist_id)) {
                            $this->wix_delete_article($exist_id);
                        }
                    }

                    //one article
//                    $iii++;
//                    if($iii==2){
//                        debug($new_article);
//                    }else{
//                        debug($new_article,false);
//                    }
                }
            }
        } while ($numPages > $page);

        $res = array(
            'ok' => true,
            'cnt' => $itemsCount,
            'time' => (microtime(true) - $start_time),
        );

        return $this->output->set_output(json_encode($res));
    }

    function _get_label_ids($labelList)
    {
        $labelIDs = [];
        foreach ($labelList as $label) {
            $labelName = $label->name;
            $labelName = trim($labelName);
            if (empty($labelName)) continue;
            $updateLabel = [];
            if (isset($this->labelList2[$labelName])) {
                $updateLabel = $this->labelList2[$labelName];
            } else {
                $updateLabel = $this->wix_add_label($labelName);
                $this->labelList2[$labelName] = $updateLabel;
            }

            $labelIDs[] = $updateLabel['id'];
        }
        return $labelIDs;
    }

    /**
     * This function is used to load the user list
     */
    function add()
    {
        $mode = $this->input->post('mode');
        if ($mode == 'save') {

            $this->data['company'] = array(
                'company_id' => NULL,
                'company_name' => $this->input->post('company_name'),
                'company_email' => $this->input->post('company_email'),
                'company_password' => $this->input->post('company_password'),
                'payment_date' => $this->input->post('payment_date'),
            );

            $this->form_validation->set_rules('company_name', '企業名', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('company_email', 'メールアドレス', 'trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('company_password', 'パスワード', 'required|max_length[20]');
            $this->form_validation->set_rules('company_password_confirm', 'パスワード（確認）', 'trim|required|matches[company_password]|max_length[20]');

            if ($this->form_validation->run() === TRUE) {

                if ($this->_check_email($this->data['company']['company_email'])) {
                    $this->data['company']['uuid'] = $this->_uuid();
                    $this->data['company']['company_password'] = sha1($this->data['company']['company_password']);
                    $this->data['company']['create_date'] = date('Y-m-d H:i:s');
                    $this->data['company']['update_date'] = date('Y-m-d H:i:s');
                    
                    $this->data['company']['payment_date'] = empty($this->data['company']['payment_date']) ?? date('Y-m-d', strtotime(date('Y-m-d'). ' + 3 months'));

                    $result = $this->company_model->add($this->data['company']);
                    if ($result) {
                        $this->session->set_flashdata('success', '正常に登録されました。');
                        $this->session->set_flashdata('error', '');

                        $company = $this->company_model->getFromUUID($this->data['company']['uuid']);

                        $this->setting_model->registerSetting('anal_date', date('Y-m-d', time() - 86400 * 30), $company['company_id']);
                        $this->setting_model->registerSetting('faq_date', date('Y-m-d', time() - 86400 * 30), $company['company_id']);

                        redirect('admin/company');

                    } else {
                        $this->session->set_flashdata('success', '');
                        $this->session->set_flashdata('error', '更新に失敗しました。');
                    }
                } else {
                    $this->session->set_flashdata('error', '既に登録されているメールアドレスです。');
                }
            } else {
//                var_dump(validation_errors());;
            }

        }

        $this->_load_view_admin("admin/company/add");

    }

    public function _uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * This function is used to check whether email already exist or not
     */
    function _check_email($email, $company_id = 0)
    {
        if (empty($company_id)) {
            $result = $this->company_model->checkEmailExists($email);
        } else {
            $result = $this->company_model->checkEmailExists($email, $company_id);
        }

        return empty($result) ? true : false;
    }

    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function delete()
    {
        $userId = $this->input->post('userId');

        $result = $this->company_model->delete($userId, 'company_id');

        if ($result > 0) {
            echo(json_encode(array('status' => TRUE)));
        } else {
            echo(json_encode(array('status' => FALSE)));
        }
    }


    /**
     * This function used to show login history
     * @param number $userId : This is user id
     */
    function loginHistoy($userId = NULL)
    {
        if ($this->isAdmin() == TRUE) {
            $this->loadThis();
        } else {
            $userId = ($userId == NULL ? 0 : $userId);

            $searchText = $this->input->post('searchText');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');

            $data["userInfo"] = $this->user_model->getUserInfoById($userId);

            $data['searchText'] = $searchText;
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;

            $this->load->library('pagination');

            $count = $this->user_model->loginHistoryCount($userId, $searchText, $fromDate, $toDate);

            $returns = $this->paginationCompress("login-history/" . $userId . "/", $count, 10, 3);

            $data['userRecords'] = $this->user_model->loginHistory($userId, $searchText, $fromDate, $toDate, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'ユーザー：ログイン履歴';

            $this->loadViews("loginHistory", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to show users profile
     */
    function profile($active = "details")
    {
        $data["userInfo"] = $this->user_model->getUserInfoWithRole($this->vendorId);
        $data["active"] = $active;

        $this->global['pageTitle'] = $active == "詳細" ? 'パスワード変更' : 'プロフィール編集';
        $this->loadViews("profile", $this->global, $data, NULL);
    }

    /**
     * This function is used to update the user details
     * @param text $active : This is flag to set the active tab
     */
    function profileUpdate($active = "details")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('fname', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|min_length[10]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]|callback_emailExists');

        if ($this->form_validation->run() == FALSE) {
            $this->profile($active);
        } else {
            $name = strtolower($this->security->xss_clean($this->input->post('fname')));
            $mobile = $this->security->xss_clean($this->input->post('mobile'));
            $email = strtolower($this->security->xss_clean($this->input->post('email')));

            $userInfo = array('name' => $name, 'email' => $email, 'mobile' => $mobile, 'updatedBy' => $this->vendorId, 'updatedDtm' => date('Y-m-d H:i:s'));

            $result = $this->user_model->editUser($userInfo, $this->vendorId);

            if ($result == true) {
                $this->session->set_userdata('name', $name);
                $this->session->set_flashdata('success', 'パスワードの更新に成功しました。');
            } else {
                $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。');
            }

            redirect('profile/' . $active);
        }
    }

    /**
     * This function is used to change the password of the user
     * @param text $active : This is flag to set the active tab
     */
    function changePassword($active = "changepass")
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('oldPassword', 'Old password', 'required|max_length[20]');
        $this->form_validation->set_rules('newPassword', 'New password', 'required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword', 'Confirm new password', 'required|matches[newPassword]|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $this->profile($active);
        } else {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            $resultPas = $this->user_model->matchOldPassword($this->vendorId, $oldPassword);

            if (empty($resultPas)) {
                $this->session->set_flashdata('nomatch', 'Your old password is not correct');
                redirect('profile/' . $active);
            } else {
                $usersData = array('password' => getHashedPassword($newPassword), 'updatedBy' => $this->vendorId,
                    'updatedDtm' => date('Y-m-d H:i:s'));

                $result = $this->user_model->changePassword($this->vendorId, $usersData);

                if ($result > 0) {
                    $this->session->set_flashdata('success', 'パスワードの更新に成功しました。');
                } else {
                    $this->session->set_flashdata('error', 'パスワードの更新に失敗しました。');
                }

                redirect('profile/' . $active);
            }
        }
    }

    /**
     * This function is used to check whether email already exist or not
     * @param {string} $email : This is users email
     */
    function emailExists($email)
    {
        $userId = $this->vendorId;
        $return = false;

        if (empty($userId)) {
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if (empty($result)) {
            $return = true;
        } else {
            $this->form_validation->set_message('emailExists', 'The {field} already taken');
            $return = false;
        }

        return $return;
    }
}

?>

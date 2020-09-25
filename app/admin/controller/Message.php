<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */


namespace app\admin\controller;

use app\model\message\Email as EmailModel;
use app\model\message\Message as MessageModel;
use app\model\message\Sms;

/**
 * 消息管理 控制器
 */
class Message extends BaseAdmin
{
    /**
     *  消息管理 列表
     */
    public function lists(){
        $message_model = new MessageModel();
        //买家消息
        $member_message_list_result = $message_model->getMessageList([["message_type", "=", 1]]);
        $member_message_list = $member_message_list_result["data"];
        $this->assign("member_message_list", $member_message_list);
        //卖家通知
        $shop_message_list_result = $message_model->getMessageList([["message_type", "=", 2]]);
        $shop_message_list = $shop_message_list_result["data"];
        $this->assign("shop_message_list", $shop_message_list);
        return $this->fetch("message/lists");
    }
    
    /**
     * 编辑短信模板(跳转)
     */
    public function editSmsMessage(){
        $keywords = input("keywords", '');
        $sms_model = new Sms();
        $edit_data_result = $sms_model->doEditSmsMessage();
        if(empty($edit_data_result["data"][0]))
            $this->error("没有开启的短信方式!");

        $edit_data = $edit_data_result["data"][0];
        $edit_url = $edit_data["edit_url"];
        $this->redirect(addon_url($edit_url, ["keywords" => $keywords]));
    }
    
    /**
     * 短信列表
     */
    public function sms(){
        if(request()->isAjax()){
            $sms_model = new Sms();
            $list = $sms_model->getSmsType();
            return $list;
        }else{
            $this->forthMenu();
            return $this->fetch("message/sms");
        }
    }

    /**
     * 短信记录
     */
    public function smsRecords(){
        if(request()->isAjax()){
            $sms_model = new Sms();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $status = input('status', 'all');
            $condition = [];
            if(!empty($search_text)){
                $condition[] = ["keywords_name", "like", "%".$search_text."%"];
            }
            if (!empty($status) && $status != 'all') {
                if ($status == -1) {
                    $condition[] = ['status', 'not in', [0, 1, '']];
                } else {
                    $condition[] = ['status', '=', $status-1];
                }
            }

            $list = $sms_model->getSmsRecordsPageList($condition, $page, $page_size);
            return $list;
        }else{
            $this->forthMenu();
            return $this->fetch("message/smsrecords");
        }
    }

    /**
     * 删除短信记录
     */
    public function deleteSmsRecords(){
        if(request()->isAjax()) {
            $ids = input("ids", "");
            $sms_model = new Sms();
            $condition = array(
                ["id", "in", $ids]
            );
            $result = $sms_model->deleteSmsRecords($condition);
            return $result;
        }
    }

    /**
     * 邮箱配置
     */
    public function email(){
        $email_model = new EmailModel();
        if(request()->isAjax()) {
            $host = input("host", '');//SMTP服务器
            $port = input("port", '');//SMTP端口
            $from = input("from", '');//发信人邮件地址
            $username = input("username", '');//SMTP身份验证用户名
            $password = input("password", '');//SMTP身份验证码
            $is_auth = input("is_auth", 0);//是否使用安全链接
            $is_use = input("is_use", 0);//是否开启
            $data = array(
                "host" => $host,
                "port" => $port,
                "from" => $from,
                "username" => $username,
                "password" => $password,
                "is_auth" => $is_auth,
            );
            $result = $email_model->setEmailConfig($data, $is_use);
            return $result;
        }else{
            $this->forthMenu();
			$config_info = $email_model->getEmailConfig();
			$this->assign('config_info', $config_info["data"]["value"]);
			$this->assign('is_use', $config_info['data']['is_use']);
            return $this->fetch("message/email");
        }
    }

    /**
     * 测试发送邮箱
     */
    public function testSendEmail(){
        $email_model = new EmailModel();
        if(request()->isAjax()) {
            $test_address = input("test_address", '');//测试邮箱地址
            if(empty($test_address))
                return error(-1, "测试邮箱地址不可为空!");

            $subject = "测试发送";
            $body = "测试发送";
            $data = array(
                "host" => input("host", ""),
                "username" => input("username", ""),
                "password" => input("password", ""),
                "port" => input("port", ""),
                "from" => input("from", ""),
                "from_name" => input("from", ""),
                "address" => $test_address,
                "subject" => $subject,
                "body" => $body,
                "is_auth" => input("is_auth", ""),
                "attachment" => input("attachment", ""),
                "attachment_name" => input("attachment_name", ""),
            );
            $res = $email_model->send($data);
            return $res;
        }
    }

    /**
     * 邮箱记录
     */
    public function emailRecords(){

        if(request()->isAjax()){
            $email_model = new EmailModel();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $status = input('status', 'all');
            $condition = [];
            if(!empty($search_text)){
                $condition[] = ["title", "like", "%".$search_text."%"];
            }
            if (!empty($status) && $status != 'all') {
                if ($status == -1) {
                    $condition[] = ['status', 'not in', [0, 1, '']];
                } else {
                    $condition[] = ['status', '=', $status-1];
                }
            }
            $list = $email_model->getEmailRecordsPageList($condition, $page, $page_size);
            return $list;
        }else{
            $this->forthMenu();
            return $this->fetch("message/emailrecords");
        }
    }

    /**
     * 删除邮件记录
     */
    public function deleteEmailRecords(){
        if(request()->isAjax()) {
            $ids = input("ids", "");
            $email_model = new EmailModel();
            $condition = array(
                ["id", "in", $ids]
            );
            $result = $email_model->deleteEmailRecords($condition);
            return $result;
        }
    }

    /**
     * 编辑短信模板(跳转)
     */
    public function editEmailMessage(){
        $message_model = new MessageModel();
        $keywords = input("keywords", "");
        $info_result = $message_model->getMessageInfo([["keywords", "=",$keywords ]]);
        $info = $info_result["data"];

        if (request()->isAjax()) {
            if(empty($info))
                return error(-1, "不存在的模板信息!");

            $email_title = input("email_title", '');//邮件标题
            $email_content = input("email_content", '');//邮件内容
            $email_is_open = input("email_is_open", 0);//邮件开关

            $data = array(
                'email_title' => $email_title,
                'email_content' => $email_content,
                "email_is_open" => $email_is_open,
            );
            $condition = array(
                ["keywords", "=", $keywords]
            );
            $this->addLog("编辑邮箱模板:".$keywords);
            $res = $message_model->editMessage($data, $condition);
            return $res;
        } else {
            if(empty($info))
                $this->error("不存在的模板信息!");

            $email_title = $info["email_title"];//邮件标题
            $email_content = $info["email_content"];//邮件内容
            $email_is_open = $info["email_is_open"];//邮件开关
            $this->assign("email_title", $email_title);
            $this->assign("email_content", $email_content);
            $this->assign("email_is_open", $email_is_open);
            $this->assign("keywords", $keywords);

            //模板变量
            $message_variable_list = $info["message_json_array"];
            $this->assign("message_variable_list", $message_variable_list);
            return $this->fetch("message/edit_email_message");
        }

    }

}
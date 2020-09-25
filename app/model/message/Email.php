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


namespace app\model\message;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 邮件管理类
 */
class Email extends BaseModel
{

    /********************************************************************* 邮件发送配置 start *********************************************************************************/
    /**
     * 设置邮件发送配置
     * array $data
     */
    public function setEmailConfig($data, $is_use)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '邮件配置', $is_use, [['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'EMAIL_CONFIG']]);
        return $res;
    }

    /**
     * 获取邮件发送配置
     */
    public function getEmailConfig()
    {
        $config = new ConfigModel();
        $res = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'EMAIL_CONFIG']]);
        return $res;
    }


    /********************************************************************* 邮件发送配置 end *********************************************************************************/


    /********************************************************************* 邮件发送记录 start *********************************************************************************/
    /**
     * 添加邮件记录
     * @param $data
     */
    public function addEmailRecords($data){
        $res = model("message_email_records")->add($data);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $res;
    }

    /**
     * 邮件记录编辑
     * @param $data
     * @param $condition
     */
    public function editEmailRecords($data, $condition){
        $res = model("message_email_records")->update($data, $condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $res;
    }

    /**
     * 删除邮箱记录
     */
    public function deleteEmailRecords($condition){
        $res = model("message_email_records")->delete($condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $res;
    }
    /**
     * 邮箱记录分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return \multitype
     */
    public function getEmailRecordsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = '*')
    {
        $list = model('message_email_records')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    /********************************************************************* 邮件发送记录 end *********************************************************************************/

    /********************************************************************* 邮件发送 start *********************************************************************************/
    /**
     * 发送消息
     * @param array $data
     */
    public function sendMessage($data = []){
        $support_type = $data["support_type"] ?? [];
        //验证是否支持邮箱发送
        if(!empty($support_type) && !in_array("email", $support_type))
            return $this->success();

        $message_info = $data["message_info"];
        //邮箱是否开启
        if(!isset($message_info["email_is_open"]) || $message_info["email_is_open"] == 0)
            return $this->error();

        //消息内容  变量替换
        $message_info = $data["message_info"];
        $title = $message_info["email_title"];//邮箱发送标题
        $content = $message_info["email_content"];//邮箱发送内容
        if(empty($title) || empty($content)){
//            return $this->error([], "发送邮件时,标题或内容不可为空!");
            $return_result = $this->error([], "EMAIL_FAIL");
        }


        $var_parse = $data["var_parse"];
        //循环替换变量解析
        foreach($message_info["message_json_array"] as $k => $v){
            if(!empty($var_parse[$k])){
                $content = str_replace("{".$v."}", $var_parse[$k], $content);
                $title = str_replace("{".$v."}", $var_parse[$k], $title);
            }
        }


        $data["subject"] = $title;
        $data["body"] = $content;

        $config_result = $this->getEmailConfig();
        $config = $config_result["data"]["value"];
        $data["host"] = $config["host"];
        $data["port"] = $config["port"];
        $data["username"] = $config["username"];
        $data["password"] = $config["password"];
        $data["from"] = $config["from"];
        $data["from_name"] = $data["site_name"];
        $data["address"] = $data["email_account"];
        $data["attachment"] = $data["attachment"] ?? '' ;
        $data["attachment_name"] = $data["attachment_name"] ?? '' ;
        $result = $this->send($data);

        //增加发送邮件记录
        $status = $result["code"] >= 0 ? 1 : 0;
        $send_time = '';
        $message_result = "发送成功";
        if($result["code"] >= 0){
            $send_time = time();
            $return_result = $this->success([], "EMAIL_SUCCESS");
        }else{
            $message_result = $result["message"];
            $return_result = $this->error([], "EMAIL_FAIL");
        }

        $records_data = array(
            "account" => $data["email_account"],
            "status" => $status,
            "title" => $title,
            "content" => $content,
            "keywords" => $data["keywords"],
            "create_time" => time(),
            "send_time" => $send_time,
            "result" => $message_result,
            "keywords_name" => $message_info["title"]
        );
        $this->addEmailRecords($records_data);
        return $return_result;
    }
    /**
     * 邮件发送
     * @param $param
     */
    public function send($param = []){
//        return $this->success();
        $mail = new PHPMailer(true);
        $host = $param["host"];// 链接域名邮箱的服务器地址
        $username = $param["username"];// smtp登录的账号
        $password = $param["password"];// smtp登录的密码 使用生成的授权码
        $port = $param["port"];// 设置ssl连接smtp服务器的远程服务器端口号
        $from = $param["from"];// 设置发件人邮箱地址 同登录账号
        $from_name = $param["from_name"];//设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $address = $param["address"];// 添加多个收件人 则多次调用方法即可
        $attachment = $param["attachment"];// 为该邮件添加附件
        $attachment_name = $param["attachment_name"];// 为该邮件添加附件重命名

        $is_html = true;// 邮件正文是否为html编码 注意此处是一个方法
        $subject = $param["subject"];//标题
        $body = $param["body"];// 添加邮件正文
        try {
            //Server settings
//            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output 设置使用ssl加密方式登录鉴权
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = $host ?? 'smtp1.example.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //smtp需要鉴权 这个必须是true  Enable SMTP authentication
            $mail->Username   = $username ?? 'user@example.com';                     // SMTP username
            $mail->Password   = $password ?? 'secret';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // 设置使用ssl加密方式登录鉴权  Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = $port ?? 465;                                    // TCP port to connect to
            $mail->CharSet= 'UTF-8';//指定字符集
            //Recipients
            $mail->setFrom($from, $from_name);
//            $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
            $mail->addAddress($address);               // Name is optional
//            $mail->addReplyTo('info@example.com', 'Information');
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

            // 附件 Attachments 判断附件是否真实存在
            if(!empty($attachment) && is_file($attachment)){
                if(!empty($attachment_name)){
                    $mail->addAttachment($attachment, $attachment_name);         // Add attachments  附件  重命名
                }else{
                    $mail->addAttachment($attachment);         // Add attachments  附件
                }
            }
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            // Content
            $mail->isHTML($is_html);                                  // Set email format to HTML
//            $mail->Subject = mbStrreplace($subject);
//            $mail->Body    = mbStrreplace($body);
            $mail->Subject = $subject;
            $mail->Body    = $body;
//            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
            return $this->success(1);
        } catch (Exception $e) {
            return $this->error("", $mail->ErrorInfo);
        }
    }
    /********************************************************************* 邮件发送 end *********************************************************************************/
}
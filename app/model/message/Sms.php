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

use app\model\BaseModel;
use overtrue\EasySms\Strategies\OrderStrategy;

/**
 * 短信管理类
 */
class Sms extends BaseModel
{
    public $config = [
        // HTTP 请求的超时时间（秒）
        'timeout' => 5.0,
        // 默认发送配置
        'default' => [
            // 网关调用策略，默认：顺序调用
            'strategy' => OrderStrategy::class,
            'gateways' => [],
        ],
    ];

    /********************************************************************* 短信类型 start *********************************************************************************/

    /**
     * 获取短信类型
     * @return array
     */
    public function getSmsType(){
        $res = event('SmsType', []);
        return $this->success($res);
    }

    /**
     * 获取短信编辑地址
     * @return array
     */
    public function doEditSmsMessage(){
        $res = event('DoEditSmsMessage', []);
        return $this->success($res);
    }
    /********************************************************************* 短信类型 end *********************************************************************************/

    /********************************************************************* 短信发送记录 start *********************************************************************************/
    /**
     * 添加短信记录
     * @param $data
     * @return array|int|string
     */
    public function addSmsRecords($data){
        $res = model("message_sms_records")->add($data);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $res;
    }

    /**
     * 短信记录编辑
     * @param $data
     * @param $condition
     * @return array|int
     */
    public function editSmsRecords($data, $condition){
        $res = model("message_sms_records")->update($data, $condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $res;
    }

    /*
     * 删除短信记录
     */
    public function deleteSmsRecords($condition){
        $res = model("message_sms_records")->delete($condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $res;
    }

    /**
     * 短信记录分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getSmsRecordsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = '*')
    {
        $list = model('message_sms_records')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    /********************************************************************* 短信发送记录 end *********************************************************************************/

    /**
     * 短信发送
     * @param array $param
     * @return array|mixed|void
     */
    public function sendMessage($param = []){
        $support_type = $param["support_type"] ?? [];//支持的消息发送方式,优先级最高
        //验证是否支持短信发送
        if(!empty($support_type) && !in_array("sms", $support_type))
            return $this->success();

        $message_info = $param["message_info"];

        //短信是否开启
        if(!isset($message_info["sms_is_open"]) || $message_info["sms_is_open"] == 0) return $this->error();
        $result = event("SendSms", $param, true);
        if(empty($result)){
            $result = $this->error([], "EMPTY_SMS_TYPE");
        }

        //增加短信邮件记录
        $status = $result["code"] >= 0 ? 1 : 0;
        $send_time = '';
        $message_result = "发送成功";
        $addon_name = "";
        $addon = "";

        if($result["code"] >= 0){
            $send_time = time();
            $addon = $result["data"]["addon"];
            $addon_name = $result["data"]["addon_name"];
            $return_result = $this->success([], "SMS_SUCCESS");
        }else{
            $message_result = $result["message"] ?? '';
            $return_result = $this->error([], "SMS_FAIL");
        }

        if(!empty($result["data"])){
            $content = $result["data"]["content"] ?? '';
        }else{
            $content = '';
        }
        $var_parse = $param["var_parse"];
        $records_data = [
            "account" => $param["sms_account"],
            "status" => $status,
            "addon" => $addon,
            "addon_name" => $addon_name,
            "content" => $content,
            "var_parse" => json_encode($var_parse),
            "keywords" => $param["keywords"],
            "create_time" => time(),
            "send_time" => $send_time,
            "result" => $message_result,
            "keywords_name" => $message_info["title"],
        ];
        $this->addSmsRecords($records_data);
        return $return_result;
    }
}
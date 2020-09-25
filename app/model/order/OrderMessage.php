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

namespace app\model\order;

use app\model\member\Member;
use app\model\message\Message;
use app\model\message\Email;
use app\model\message\Sms;
use app\model\BaseModel;
use addon\wechat\model\Message as WechatMessage; 

/**
 * 订单消息操作
 *
 * @author Administrator
 *
 */
class OrderMessage extends BaseModel
{
    /**
     * 订单生成提醒
     * @param $data
     */
    public function messageOrderCreate($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "full_address,address,order_no,mobile,member_id,order_type,create_time,order_name,order_money");
        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }
        //绑定微信公众号才发送
        if (!empty($member_info) && !empty($member_info["wx_openid"])) {
            $wechat_model = new WechatMessage();
            $data["openid"] = $member_info["wx_openid"];
            $data["template_data"] = [
                'keyword1' => $order_info['order_no'],
                'keyword2' => time_to_date($order_info['create_time']),
                'keyword3' => str_sub($order_info['order_name']),
                'keyword4' => $order_info['order_money'],
                'keyword5' => $order_info['full_address'].$order_info['address']." ".$order_info['mobile']
            ];
            $data["page"] = $this->handleUrl($order_info['order_type'], $order_id); 
            $wechat_model->sendMessage($data);
        }

    }
    


    /**
     * 消息发送——支付成功
     * @param $params
     * @return array|mixed|void
     */
    public function messagePaySuccess($params)
    {
        // 发送短信
        $var_parse = [
            "orderno"    => $params['order_no'],
            "username"   => $params["name"],
            "ordermoney" => $params["order_money"],
        ];
        $params["sms_account"] = $params["mobile"] ?? '';//手机号
        $params["var_parse"] = $var_parse;
        $sms_model = new Sms();
        $sms_result = $sms_model->sendMessage($params);

        //邮箱发送
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $params["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $params["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($params);
        }


        $data = $params;
        //绑定微信公众号才发送
        if (!empty($member_info) && !empty($member_info["wx_openid"])) {
            $wechat_model = new WechatMessage();
            $data["openid"] = $member_info["wx_openid"];
            $data["template_data"] = [
                'keyword1' => time_to_date($params['create_time']),
                'keyword2' => $params['order_no'],
                'keyword3' => str_sub($params['order_name']),
                'keyword4' => $params['order_money'],
            ];
            $data["page"] = $this->handleUrl($params['order_type'], $params["order_id"]);
            $wechat_model->sendMessage($data);
        }

    }


    /**
     * 发送支付消息
     * @param array $info
     */
    public function sendMessagePay($info = [])
    {
        // 支付通知
        $param = ["keywords" => "ORDER_PAY"];
        $param = array_merge($param, $info);
        $message_model = new Message();
        $message_model->sendMessage($param);
    }

    /**
     * 订单关闭提醒
     * @param $data
     */
    public function messageOrderClose($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_type,order_no,mobile,member_id,order_name,create_time,order_money,close_time");
        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }

        if (!empty($member_info) && !empty($member_info["wx_openid"])) {
            $wechat_model = new WechatMessage();
            $data["openid"] = $member_info["wx_openid"];
            $data["template_data"] = [
                'keyword1' => str_sub($order_info['order_name']),
                'keyword2' => $order_info['order_no'],
                'keyword3' => time_to_date($order_info['create_time']),
                'keyword4' => $order_info['order_money'],
                'keyword5' => time_to_date($order_info['close_time'])
            ];
            $data["page"] = $this->handleUrl($order_info['order_type'], $order_id);
            $wechat_model->sendMessage($data);
        }

    }

    /**
     * 订单完成提醒
     * @param $data
     */
    public function messageOrderComplete($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_type,order_no,mobile,member_id,order_name,create_time");

        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }

        //发送模板消息
        $wechat_model = new WechatMessage();
        $data["openid"] = $member_info["wx_openid"];
        $data["template_data"] = [
            'keyword1' => $order_info['order_no'],
            'keyword2' => str_sub($order_info['order_name']),
            'keyword3' => time_to_date($order_info['create_time']),
        ];
        $data["page"] = $this->handleUrl($order_info['order_type'], $order_id);
        $wechat_model->sendMessage($data);

    }

    /**
     * 订单发货提醒
     * @param $data
     */
    public function messageOrderDelivery($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_type,order_no,mobile,member_id,order_name,goods_num,order_money,delivery_time");

        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }

        //发送模板消息
        $wechat_model = new WechatMessage();
        $data["openid"] = $member_info["wx_openid"];
        $data["template_data"] = [
            'keyword1' => $order_info['order_no'],
            'keyword2' => str_sub($order_info['order_name']),
            'keyword3' => $order_info['goods_num'],
            'keyword4' => $order_info['order_money'],
            'keyword5' => time_to_date($order_info['delivery_time']),
        ];
        $data["page"] = $this->handleUrl($order_info['order_type'], $order_id);
        $wechat_model->sendMessage($data);

    }

    /**
     * 订单收货提醒
     * @param $data
     */
    public function messageOrderTakeDelivery($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_type,order_no,mobile,member_id,full_address,address,name,order_name,sign_time");

        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }

        //发送模板消息
        $wechat_model = new WechatMessage();
        $data["openid"] = $member_info["wx_openid"];
        $data["template_data"] = [
            'keyword1' => $order_info['full_address'].$order_info['address'],
            'keyword2' => $order_info["name"],
            'keyword3' => $order_info['order_no'],
            'keyword4' => $order_info['order_name'],
            'keyword5' => time_to_date($order_info['sign_time']),
        ];
        $data["page"] = $this->handleUrl($order_info['order_type'], $order_id);
        $wechat_model->sendMessage($data);
    }

    /**
     * 订单退款同意提醒
     * @param $data
     */
    public function messageOrderRefundAgree($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_type,order_no,mobile,member_id");

        $order_goods_info = model("order_goods")->getInfo([["order_goods_id", "=", $data["order_goods_id"]]], "refund_apply_money,refund_time,refund_action_time");
        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }

        //发送模板消息
        $wechat_model = new WechatMessage();
        $data["openid"] = $member_info["wx_openid"];
        $data["template_data"] = [
            'keyword1' => $order_info['order_no'],
            'keyword2' => $order_goods_info["refund_apply_money"],
            'keyword3' => time_to_date($order_goods_info['refund_time']),
        ];
        $data["page"] = $this->handleUrl($order_info['order_type'], $order_id);
        $wechat_model->sendMessage($data);
    }
    /**
     * 订单退款拒绝提醒
     * @param $data
     */
    public function messageOrderRefundRefuse($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_type,order_no,mobile,member_id");
        $order_goods_info = model("order_goods")->getInfo([["order_goods_id", "=", $data["order_goods_id"]]], "refund_apply_money,refund_time,refund_action_time");
        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);

        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }

        //发送模板消息
        $wechat_model = new WechatMessage();
        $data["openid"] = $member_info["wx_openid"];
        $data["template_data"] = [
            'keyword1' => $order_info['order_no'],
            'keyword2' => $order_goods_info["refund_apply_money"],
            'keyword3' => time_to_date($order_goods_info['refund_action_time']),
        ];
        $data["page"] = $this->handleUrl($order_info['order_type'], $order_id);
        $wechat_model->sendMessage($data);
    }

    /**
     * 订单核销通知
     * @param $data
     */
    public function messageOrderVerify($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_type,order_no,mobile,member_id,order_name,goods_num,sign_time");

        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $data["sms_account"] = $order_info["mobile"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo([["member_id", "=", $order_info["member_id"]]]);
        $member_info = $member_info_result["data"];
        //有邮箱才发送
        if(!empty($member_info) && !empty($member_info["email"])){
            $data["email_account"] = $member_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }
    }

    /**
     * 买家发起退款，卖家通知
     * @param $data
     */
    public function messageOrderRefundApply($data){
        //发送短信
        $sms_model = new Sms();
        $order_goods_id = $data["order_goods_id"];
        $order_goods_info = model('order_goods')->getInfo(['order_goods_id' => $order_goods_id], 'order_id,refund_no,refund_reason,refund_apply_money,sku_name');

        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_goods_info['order_id'] ] ], "order_no,mobile,member_id,site_id,name");
        $var_parse = array(
            "username" => $order_info["name"],//会员名
            "goodsname" => $order_goods_info["sku_name"],//商品名称
            "orderno" => $order_info["order_no"],//商品名称
            "refundmoney" => $order_goods_info["refund_apply_money"],//退款申请金额
            "refundreason" => $order_goods_info["refund_reason"],//退款原因
            "refundno" => $order_goods_info["refund_no"],//退款原因
        );
        $site_id = $order_info['site_id'];
        $shop_info = model("shop")->getInfo([ [ "site_id", "=", $site_id ] ], "telephone,email");

        $data["sms_account"] = $shop_info["telephone"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        if(!empty($shop_info) && !empty($shop_info["email"])){
            $data["email_account"] = $shop_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }
    }

    /**
     * 买家已退款，卖家通知
     * @param $data
     */
    public function messageOrderRefundDelivery($data){
        //发送短信
        $sms_model = new Sms();
        $order_id = $data["order_id"];
        $order_info = model("order")->getInfo([ [ "order_id", "=", $order_id ] ], "order_no,mobile,member_id,site_id");

        $var_parse = array(
            "orderno" => $order_info["order_no"],//商品名称
        );
        $site_id = $order_info['site_id'];
        $shop_info = model("shop")->getInfo([ [ "site_id", "=", $site_id ] ], "telephone,email");

        $data["sms_account"] = $shop_info["telephone"];//手机号
        $data["var_parse"] = $var_parse;
        $sms_model->sendMessage($data);
        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        if(!empty($shop_info) && !empty($shop_info["email"])){
            $data["email_account"] = $shop_info["email"];//邮箱号
            $email_model->sendMessage($data);
        }
    }

    /**
     * 处理订单链接
     * @param unknown $order_type
     * @param unknown $order_id
     * @return string
     */
    private function handleUrl($order_type, $order_id){
        switch ($order_type) {
            case 2:
                return 'pages/order/detail_pickup/detail_pickup?order_id=' . $order_id;
                break;
            case 3:
                return 'pages/order/detail_local_delivery/detail_local_delivery?order_id=' . $order_id;
                break;
            case 4:
                return 'pages/order/detail_virtual/detail_virtual?order_id=' . $order_id;
                break;
            default:
                return 'pages/order/detail/detail?order_id=' . $order_id;
                break;
        }
    }
}
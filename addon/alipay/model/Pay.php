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

namespace addon\alipay\model;

use addon\alipay\data\sdk\AopClient;
use addon\alipay\data\sdk\request\AlipayTradeWapPayRequest;
use addon\alipay\data\sdk\request\AlipayTradePagePayRequest;
use addon\alipay\data\sdk\request\AlipayTradeAppPayRequest;
use addon\alipay\data\sdk\request\AlipayTradeCloseRequest;
use addon\alipay\data\sdk\request\AlipayTradeRefundRequest;
use app\model\system\Pay as PayCommon;
use app\model\BaseModel;
use think\facade\Log;
use addon\alipay\data\sdk\request\AlipayFundTransToaccountTransferRequest;

/**
 * 支付宝支付配置
 */
class Pay extends BaseModel
{

    public $aop;

    function __construct()
    {
        // 获取支付宝支付参数(统一支付到平台账户)
        $config_model = new Config();
        $config_result = $config_model->getPayConfig();
        $config = $config_result["data"];
        if(!empty($config)){
            $config_info = $config["value"];
        }

        // 获取支付宝支付参数(统一支付到平台账户)
        $this->aop = new AopClient();
        $this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $this->aop->appId = $config_info["app_id"] ?? "";
        $this->aop->rsaPrivateKey = $config_info['private_key'] ?? "";
        $this->aop->alipayrsaPublicKey =$config_info['public_key'] ?? "";
        $this->aop->alipayPublicKey = $config_info['alipay_public_key'] ?? "";
        $this->aop->apiVersion = '1.0';
        $this->aop->signType = 'RSA2';
        $this->aop->postCharset = 'UTF-8';
        $this->aop->format = 'json';
    }

    /**
     * 生成支付
     * @param $param
     */
    public function pay($param){
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "out_trade_no" => $param["out_trade_no"],
            "subject" => str_sub($param["pay_body"], 15),
            "total_amount" => (float)$param["pay_money"],
            "body" => str_sub($param["pay_body"], 60),
            "product_code" => 'FAST_INSTANT_TRADE_PAY',
        );
        $parameter = json_encode($parameter);
        switch ($param["app_type"])
        {
            case "h5":
                $request = new AlipayTradeWapPayRequest();
                break;
            case "pc":
                $request = new AlipayTradePagePayRequest();
                break;
            case "app":
                $request = new AlipayTradeAppPayRequest();
                break;
        }

        $request->setBizContent($parameter);
        $request->SetReturnUrl($param["return_url"]);
        $request->SetNotifyUrl($param["notify_url"]);
        $result = $this->aop->pageExecute($request, 'get');
        return $this->success([
            'type' => 'url',
            'data' => $result
        ]);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return $this->success();
        } else {
            return $this->error("", $resultCode);
        }
    }

    /**
     * 支付关闭
     * @param unknown $orderNumber
     * @return multitype:number string |multitype:number mixed
     */
    public function close($param)
    {
        $parameter = array(
            "out_trade_no" => $param["out_trade_no"]
        );
        // 建立请求
        $request = new AlipayTradeCloseRequest();
        $request->setBizContent(json_encode($parameter));
        $result = $this->aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return $this->success();
        } else {
            return $this->error("", $resultCode->sub_msg);
        }
    }

    /**
     * 支付宝支付原路返回
     * @param unknown $param  支付参数
     */
    public function refund($param)
    {
        $pay_info = $param["pay_info"];
        $refund_no = $param["refund_no"];
        $out_trade_no = $pay_info["trade_no"] ?? '';
        $refund_fee = $param["refund_fee"];
        $parameter = array(
            'trade_no' => $out_trade_no,
            'refund_amount' => sprintf("%.2f", $refund_fee),
            'out_request_no' => $refund_no
        );
        // 建立请求
        $request = new AlipayTradeRefundRequest ();
        $request->setBizContent(json_encode($parameter));
        $result = $this->aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return $this->success();
        } else {
            return $this->error("", $resultCode->sub_msg);
        }
    }

    /**
     * 支付宝转账
     * @param $param
     * @return array
     * @throws \think\Exception
     */
    public function payTransfer($param)
    {
        try {
            $config_model = new Config();
            $config_result = $config_model->getPayConfig();
            if ($config_result['code'] < 0) return $config_result;
            $config = $config_result['data']['value'];
            if (empty($config)) return $this->error([], '平台未配置支付宝支付');
            if (!$config['transfer_status']) return $this->error([], '平台未启用支付宝转账');
            
            $parameter = [
                'out_biz_no' => $param['out_trade_no'],
                'payee_type' => 'ALIPAY_LOGONID',
                'payee_account' => $param["account_number"],
                'amount' => sprintf("%.2f", $param['amount']),
                'payee_real_name' => $param["real_name"],
                'remark' => $param["desc"]
            ];
            // 建立请求
            $request = new AlipayFundTransToaccountTransferRequest();
            $request->setBizContent(json_encode($parameter));
            $result = $this->aop->execute($request);
            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            $resultCode = $result->$responseNode->code;
            if(!empty($resultCode) && $resultCode == 10000){
                return $this->success([
                    'out_trade_no' => $result->$responseNode->out_biz_no, // 商户交易号
                    'payment_no' => $result->$responseNode->order_id, // 微信付款单号
                    'payment_time' => date_to_time($result->$responseNode->pay_date) // 付款成功时间
                ]);
            } else {
                return $this->error([], $result->$responseNode->sub_msg);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 异步完成支付
     * @param $param
     */
    public function payNotify(){

//        Log::write('pay_notifiy_log:alipay:'.json_encode(input()), 'notice');
        try{
            $res = $this->aop->rsaCheckV1($_POST, $this->aop->alipayrsaPublicKey, $this->aop->signType);
            if ($res) { // 验证成功
                $out_trade_no = $_POST['out_trade_no'];
                // 支付宝交易号
                $trade_no = $_POST['trade_no'];
                // 交易状态
                $trade_status = $_POST['trade_status'];
                $pay_common = new PayCommon();
                if($trade_status == "TRADE_SUCCESS"){
                    $retval = $pay_common->onlinePay($out_trade_no, "alipay", $trade_no, "alipay");
                }
                echo "success";
            } else {
                // 验证失败
                echo "fail";
            }
        } catch (\Exception $e) {
            echo "fail";
        }
    }

}
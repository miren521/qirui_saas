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


namespace addon\shopwithdraw\model;

use app\model\BaseModel;
use app\model\member\Member;
use app\model\shop\ShopAccount;

/**
 * 店铺提现
 */
class Withdraw extends BaseModel
{


    /**
     * 转账
     * @param $condition
     */
    public function transfer($id){
        $shop_account_model = new ShopAccount();

        $info_result = $shop_account_model->getShopWithdrawInfo([["id", "=", $id]]);
        if(empty($info_result["data"]))
            return $this->error();

        $info = $info_result["data"];
        $transfer_type = $this->getBankType()[$info["bank_type"]];
        $pay_data = array(
            "out_trade_no" => $info["withdraw_no"],
            "real_name" => $info["settlement_bank_account_name"],
            "amount" => $info["money"],
            "desc" => "店铺提现".$info["memo"],
            "transfer_type" => $transfer_type,
            "account_number" => $info["settlement_bank_account_number"]
        );

        //调用在线转账借口
        $pay_result = event("PayTransfer", $pay_data, true);
        if(empty($pay_result)){
            $pay_result = $this->error();
        }
        if($pay_result["code"] < 0 ){
            return $pay_result;
        }
        //调用完成转账
        $result = $shop_account_model->shopWithdrawPass($id, []);
        return $result;

    }

    /**
     * 转账方式
     */
    public function getTransferType(){
        $data = array(
            "bank" => "银行卡"
        );
        $temp_array = event("TransferType", []);

        if(!empty($temp_array)){
            foreach($temp_array as $k => $v){
                $data[$v["type"]] = $v["type_name"];
            }
        }
        return $data;
    }


    /**
     * 无意义函数
     */
    public function getBankType(){
        $bank_type = array(
            1 => "bank",
            2 => "alipay",
            3 => "wechatpay",
        );
        return $bank_type;
    }
}
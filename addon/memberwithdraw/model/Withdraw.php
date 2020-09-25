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


namespace addon\memberwithdraw\model;

use app\model\BaseModel;
use app\model\member\Withdraw as MemberWithdraw;

/**
 * 会员提现
 */
class Withdraw extends BaseModel
{
	

	
	/**
	 * 转账
	 * @param $condition
	 */
	public function transfer($id)
	{
            $withdraw_model = new MemberWithdraw();
            $info_result = $withdraw_model->getMemberWithdrawInfo([ [ "id", "=", $id ] ], "withdraw_no,account_number,realname,money,memo,transfer_type");
            if (empty($info_result["data"]))
                return $this->error();

            $info = $info_result["data"];
		if(!in_array($info["transfer_type"], ["wechatpay","alipay"]))
		    return $this->error('', "当前提现方式不支持在线转账");


		$pay_data = array(
			"out_trade_no" => $info["withdraw_no"],
			"real_name" => $info["realname"],
			"amount" => $info["money"],
			"desc" => "会员提现".$info["memo"],
			"transfer_type" => $info["transfer_type"],
			"account_number" => $info["account_number"]
		);
		//调用在线转账借口
		$pay_result = event("PayTransfer", $pay_data, true);
		if (empty($pay_result)) {
			$pay_result = $this->error();
		}
		if ($pay_result["code"] < 0) {
			return $pay_result;
		}
		//调用完成转账
		$result = $withdraw_model->transferFinish([ [ "id", "=", $id ] ]);
		return $result;
		
	}
	

}
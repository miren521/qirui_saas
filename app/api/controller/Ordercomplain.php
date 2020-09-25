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


namespace app\api\controller;

use app\model\member\Member as MemberModel;
use app\model\order\Complain;
use app\model\order\OrderRefund as OrderRefundModel;

class Ordercomplain extends BaseApi
{
	
	/**
	 * 发起维权
	 */
	public function complain()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$member_model = new MemberModel();
		$member_info_result = $member_model->getMemberInfo([ [ "member_id", "=", $this->member_id ] ]);
		$member_info = $member_info_result["data"];
		$complain_model = new Complain();
		$order_goods_id = isset($this->params['order_goods_id']) ? $this->params['order_goods_id'] : '0';
		$complain_reason = isset($this->params['complain_reason']) ? $this->params['complain_reason'] : '';
		$complain_remark = isset($this->params['complain_remark']) ? $this->params['complain_remark'] : '';
		$data = array(
			"order_goods_id" => $order_goods_id,
			"complain_reason" => $complain_reason,
			"complain_remark" => $complain_remark
		);
		$result = $complain_model->complainApply($data, $member_info);
		return $this->response($result);
	}
	
	/**
	 * 取消发起的平台维权申请
	 */
	public function cancel()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$member_model = new MemberModel();
		$member_info_result = $member_model->getMemberInfo([ [ "member_id", "=", $this->member_id ] ]);
		$member_info = $member_info_result["data"];
		$complain_model = new Complain();
		$order_goods_id = isset($this->params['order_goods_id']) ? $this->params['order_goods_id'] : '0';
		$data = array(
			"order_goods_id" => $order_goods_id
		);
		$res = $complain_model->cancelComplain($data, $member_info);
		return $this->response($res);
	}
	
	
	/**
	 * 维权详情
	 * @return string
	 */
	public function detail()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$order_goods_id = isset($this->params['order_goods_id']) ? $this->params['order_goods_id'] : '0';
		$complain_model = new Complain();
		$order_refund_model = new OrderRefundModel();
        $refund_condition = array(
            ['order_goods_id', '=', $order_goods_id],
            ['member_id', '=', $this->member_id]
        );
		$order_goods_info_result = $complain_model->getRefundDetail($refund_condition);
		$order_goods_info = $order_goods_info_result["data"];//订单项信息
		$refund_money = $order_refund_model->getOrderRefundMoney($order_goods_id);
		$refund_reason_type = $order_refund_model->refund_reason_type;
		
		//维权信息
		$complain_info_result = $complain_model->getMemberComplainDetail($order_goods_id, $this->member_id);
		$complain_info = $complain_info_result["data"];
		$result = array(
			"order_goods_info" => $order_goods_info,
			"refund_money" => $refund_money,
			"refund_reason_type" => $refund_reason_type,
			"complain_info" => $complain_info
		);
		
		return $this->response($this->success($result));
	}
	
}